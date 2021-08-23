<?php
require_once ("../includes/config.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        //sendMail("ruslan.sarachan@gmail.com", "bitaps It works!!!", json_encode($_POST).'----'.json_encode($_GET), $this->emailHeader);
        //exit($_POST['invoice']);
        /*
                $_POST = json_decode('{"payout_service_fee":"20000","amount":"11200","payout_miner_fee":"9690","code":"PMTv2rKj2Ng8nXvxHpAjBTD74mHFCKNmnDnU1NSpowkKarZpMDwCY","confirmations":"6","address":"17dxzySSk3YM8qHW4hiFJpNGjnDu2bDhWT","invoice":"invQ83x52A8CD5eRCW4R43zvhc9mPdHsCezzkK9jqZuTGeQZAccfz","tx_hash":"588E57681E7F43017BC1868A275DC6AB680A607CDA1B580C87E51BC820C2438E","payout_tx_hash":"3d6f0bf3d6dbfcf8054ae6f8c2fb93d1b630d254b0328d2978aea86d468fbd4a","transaction_id":"8bea8c69b2ff612d6a96e1385ed09605"} ',true);
        */
        $adminEmail = $this->db->GetOne ("Select value From settings Where keyname='ContactEmail'");

//debug($_POST, false);

        /*
        tx_hash={transaction hash}
        address={address}
        invoice={invoice}
        code={payment code}
        amount={amount} # Satoshi
        confirmations={confirmations}
        payout_tx_hash={transaction hash} # payout transaction hash
        payout_miner_fee={amount}
        payout_service_fee={amount}
        */

        $transaction_hash = $this->GetGP("tx_hash", null) ;
        $transaction_id = $this->GetGP("transaction_id", null) ;
        $confirmations = $this->GetGP("confirmations", null) ;
        $value_in_satoshi = $this->GetGP("amount", null) ;
        $value_in_btc = $value_in_satoshi / 100000000;

        $payout_tx_hash = $this->GetGP("payout_tx_hash", null) ;
        $payout_miner_fee = $this->GetGP("payout_miner_fee", null) ;
        $payout_service_fee = $this->GetGP("payout_service_fee", null) ;
        $invoice = $this->GetGP("invoice", null) ;

        echo $invoice;

        if ( $payout_tx_hash == 'None' )
        {
            $payout_tx_hash = null ;
            $payout_miner_fee = null ;
            $payout_service_fee = null ;
        }
        else
        {
            $payout_miner_fee = $payout_miner_fee / 100000000; ;
            $payout_service_fee = $payout_service_fee / 100000000; ;
        }

        $dataPost = json_encode($_POST);

        require_once("../includes/bitaps/config.php");
        $class = new Bitcoin();
        $data = $class->setNotify($transaction_id, $transaction_hash, $confirmations, $payout_miner_fee, $payout_service_fee, $dataPost);
        if (empty($data)) exit();

        //$confirmations < BTC_CONFIRM ||
        if ( !$payout_tx_hash) exit();


        //sendMail("ruslan.sarachan@gmail.com", "bitaps It works!!!", "confirmations=$confirmations, transaction_id=$transaction_id", $this->emailHeader);

        $codeProcessor = 12;

        // get total prices from DB
        $member_id = $data['member_id']; //member id
        $descr = $data['type']; //Payment_for_eBook
        $amount = $data['amount'];
        $item_level = $data['m_level']; //m_lemel (2)
        $txnID = $transaction_id;

        $count = $this->db->GetOne ("Select Count(*) from `payins` where transaction_id='$txnID'", -1);
        if ($count>0) exit('');

        switch ($descr)
        {
            case "Payment_for_level":
                $sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
                //if ($member_id==70466) $amount=1;
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='bitaps'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);
                //sendMail("ruslan.sarachan@gmail.com", "bitaps It works!!!", "value_in_btc:$value_in_btc >= {$data['amount']}", $this->emailHeader);
                if ($count == 1 And $value_in_btc >= $data['amount'] ) //$amount >= $sum
                {
                    //sendMail("ruslan.sarachan@gmail.com", "bitaps It works!!!", "payUpline", $this->emailHeader);
                    payUpline ($member_id, $txnID, $item_level, $codeProcessor);
                    $subject = "Member payment report";
                    $message = "Member ID=$member_id made a bitaps payment";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                else
                {
                    $subject = "Mistake payment report";
                    $message = "Member ID=$member_id attempted to make a payment\r\n";
                    $message .= "bitaps returned the next variables:\r\n";
                    $message .= "PAYMENT_AMOUNT(summ)=$value_in_btc, DESCRIPTION(description)=$descr,
                            PAYMENT_BATCH_NUM(transaction_id)=$txnID ";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                break;

            case "Membership Fee":
                $sum = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='bitaps'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                if ($count == 1 And $value_in_btc >= $data['amount'] ) // $amount >= $sum
                {

                    $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$member_id'");
                    $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                    if ($enr_level == 0)
                    {
                        $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                        $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$member_id'");
                    }
                    payUpline ($member_id, $txnID, $item_level, $codeProcessor);
                    $subject = "Member payment report";
                    $message = "Member ID=$member_id made a bitaps payment";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                else
                {

                    $subject = "Mistake payment report";
                    $message = "Member ID=$member_id attempted to make a payment\r\n";
                    $message .= "bitaps returned the next variables:\r\n";
                    $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                            PAYMENT_BATCH_NUM(transaction_id)=$txnID";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                break;

            case "Product Payment":
                $sum = $this->db->GetOne ("Select price From `products` Where product_id='$item_level'");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='bitaps'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                if ($count == 1 And $value_in_btc >= $data['amount']) // $amount >= $sum
                {
                    payProduct ($member_id, $txnID, $item_level, $codeProcessor);

                    $subject = "Member product payment report";
                    $message = "Member ID=$member_id made a product payment";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                else
                {
                    $subject = "Mistake product payment report";
                    $message = "Member ID=$member_id attempted to make a product payment\r\n";
                    $message .= "bitaps returned the next variables:\r\n";
                    $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                            PAYMENT_BATCH_NUM(transaction_id)=$txnID";
                    sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                }
                break;

        }

        exit();

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("");

$zPage->RunController ();

?>
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

        
        $siteEmail = $this->db->GetSetting ("ContactEmail");
        
        $testMail = "";
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
        }
        
        $payment_id = $this->GetGP ("PAYMENT_ID");
        $timestamp = $this->GetGP ("TIMESTAMPGMT");
        
        $merchantAccount = $this->GetGP ("PAYEE_ACCOUNT");
        $payerAccount = $this->GetGP ("PAYER_ACCOUNT");
        
        $hash2 = $this->GetGP ("V2_HASH");
        
        $amount = $this->GetGP ("PAYMENT_AMOUNT");
        $currency = $this->GetGP ("PAYMENT_UNITS");
        $txnID = $this->GetGP ("PAYMENT_BATCH_NUM");        
        
        $member_id = $this->GetGP ("MEMBER_ID");
        $item_level = $this->GetGP ("LEVEL_ID");
        $descr = $this->GetGP ("PRODUCT");
        
        $processor = $this->db->GetEntry ("Select * From `processors` Where code='perfectmoney'");
        $passphrase = $processor ["extra_field"];
        $processor_fee = $processor ["fee"];
        $processor_account = $processor ["account_id"];
        $cycling = $this->db->GetSetting ("cycling", 0);
        
        //checking hash
        //$passphrase = strtoupper (md5 ("someword"));
        //PAYMENT_ID:PAYEE_ACCOUNT:PAYMENT_AMOUNT:PAYMENT_UNITS:PAYMENT_BATCH_NUM:PAYER_ACCOUNT:AlternateMerchantPassphraseHash:TIMESTAMPGMT
        
        //$hash = strtoupper (md5 ("$payment_id:$merchantAccount:$amount:$currency:$txnID:$payerAccount:$passphrase:$timestamp"));
        $hash = strtoupper(md5(strtoupper("$payment_id:$merchantAccount:$amount:$currency:$txnID:$payerAccount:".md5($passphrase).":$timestamp")));
        //$hash = md5("$payment_id:$merchantAccount:$amount:$currency:$txnID:$payerAccount:$passphrase:$timestamp");
        
//        sendMail("oppsofts@gmail.com", "TestPerfectMoney OppSofts - 2", "$hash == $hash2", $this->emailHeader);
//			sendMail ("ruslan.sarachan@gmail.com", "bono - TestPerfectMoney OppSofts  - 3", "$hash:md5($payment_id:$merchantAccount:$amount:$currency:$txnID:$payerAccount:".strtoupper(md5($passphrase)).":$timestamp) == $hash2:$payment_id:$merchantAccount:$amount:$currency:$txnID:$payerAccount:$passphrase:$timestamp", $this->emailHeader);
         
        
        if ($hash == $hash2)
        {
            
            switch ($descr)
            {
                case "Membership Fee":
                    
                    $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                    if ($count_txnID > 0) $txnID = "";

                    $m_id = ($cycling == 2)? 3 : 2;
                    $sum = $this->db->GetOne ("Select entrance_fee From `matrixes` Where `matrix_id`='$m_id'");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                    if ($count == 1 And $amount == $sum And $txnID != "" And $processor_account == $merchantAccount And $currency == "EUR")
                    {
                        payUpline ($member_id, $txnID, $item_level, '7');
                        $subject = "Member payment report";
                        $message = "Member ID=$member_id made a PM payment";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {
  
                        $subject = "Mistake payment report";
                        $message = "Member ID=$member_id attempted to make a PM payment\r\n";
                        $message .= "PM returned the next variables:\r\n";
                        $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                        PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                break;
                
                case "Payment_for_level":
                    $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                    if ($count_txnID > 0) $txnID = "";

                    $sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                    if ($count == 1 And $amount == $sum And $txnID != "" And $processor_account == $merchantAccount And $currency == "EUR")
                    {
                        payUpline ($member_id, $txnID, $item_level, '7');
                        $subject = "Member payment report";
                        $message = "Member ID=$member_id made a PM payment";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {
  
                        $subject = "Mistake payment report";
                        $message = "Member ID=$member_id attempted to make a PM payment\r\n";
                        $message .= "PM returned the next variables:\r\n";
                        $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                        PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                break;
                
                case "Product Payment":
                
                    $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                    if ($count_txnID > 0) $txnID = "";
                    $sum = $this->db->GetOne ("Select price From `products` Where product_id='$item_level'");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);


                    if ($count == 1 And $amount == $sum And $txnID != "" And $processor_account == $merchantAccount And $currency == "EUR")
                    {
                        payProduct ($member_id, $txnID, $item_level, '7');
                        $subject = "Member product payment report";
                        $message = "Member ID=$member_id made a PM product payment";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {

                        $subject = "Mistake product payment report";
                        $message = "Member ID=$member_id attempted to make a PM product payment\r\n";
                        $message .= "PM returned the next variables:\r\n";
                        $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                        PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                        sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                    }
                break;
                
                case "CashOut":
                    $cash_out_id = $member_id;
                    $fee = $this->db->GetSetting("fee");
                    $SiteTitle = $this->db->GetSetting ("SiteTitle");
                    $this->db->ExecuteSql ("Update `cash_out` Set status=1, amount=amount-'$fee' Where cash_out_id='$cash_out_id'");
                
                    //email notification
                    $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='16'", "");
            
                    if ($row ["is_active"] == 1 and $member_id > 0)
                    {
                    
                        $amount = $this->db->GetOne ("Select amount From `cash_out` Where cash_out_id='$cash_out_id'");
                        $member_id = $this->db->GetOne ("Select member_id From `cash_out` Where cash_out_id='$cash_out_id'");
                        $first_name = $this->dec ($this->db->GetOne ("Select first_name From `members` Where member_id='$member_id'"));
                        $last_name = $this->dec ($this->db->GetOne ("Select last_name From `members` Where member_id='$member_id'"));
                        $email = $this->db->GetOne ("Select email From `members` Where member_id='$member_id'");
                        $username = $this->db->GetOne ("Select username From `members` Where member_id='$member_id'");
                
                        $subject = $this->dec ($row ["subject"]);
                        $message = $this->dec ($row ["message"]);
                        $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                        $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
                        $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                        $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                        $message = preg_replace ("/\[Username\]/", $username, $message);
                        $message = preg_replace ("/\[Email\]/", $email, $message);
                        $message = preg_replace ("/\[Amount\]/", $amount, $message);
                        sendMail ($email, $subject, $message, $this->emailHeader);
            
                    }
                break;
                
                default:
                break;
                
                
            }
        }
        else
        {
            $subject = "Member false payment report";
            $message = "Member ID=$member_id tried to make a false PM payment";
            sendMail ($siteEmail, $subject, $testMail, $this->emailHeader);
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("perfectmoney");

$zPage->RunController ();

?>
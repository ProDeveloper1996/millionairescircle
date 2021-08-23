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
        $this->orderDefault = "member_id";
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $txnID  = $this->GetGP ("ap_referencenumber") ;
        $status = $this->GetGP ("ap_status");
        $purchase_type = $this->GetGP ("ap_purchasetype");   // Should be 'Item'
        $account_id2 = $this->GetGP ("ap_merchant");
        $security_code2 = $this->GetGP ("ap_securitycode");
        $descr = $this->GetGP ("apc_1");
        $member_id = $this->GetGP ("apc_2");
        $item_level = $this->GetGP ("apc_3");
        $amount = $this->GetGP ("ap_totalamount");
        $date_now = time ();

        $testMail = "";
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
        }

        $security_code = $this->db->GetOne ("Select extra_field From processors Where code='alertpay'");

        $siteEmail = $this->db->GetSetting ("ContactEmail");
        $siteTitle = $this->db->GetSetting ("SiteTitle");

//        sendMail ("alnik@rambler.ru", "TestGoldAlert", $testMail, $this->emailHeader);

        switch ($descr)
        {
            case "Product Payment":
                $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                if ($count_txnID > 0) $txnID = "";

                $sum = $this->db->GetOne ("Select price From `products` Where product_id='$item_level'");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='alertpay'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);


                if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Success" And $security_code2 == $security_code)
                {

                    $thisTime = time ();
                    payProduct ($member_id, $txnID, $item_level, '3');
                    $subject = "Member product payment report";
                    $message = "Member ID=$member_id made a product payment";
                    sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                }
                else
                {

                    $subject = "Mistake product payment report";
                    $message = "Member ID=$member_id attempted to make a product payment\r\n";
                    $message .= "AlertPay returned the next variables:\r\n";
                    $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                    PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                    sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                }
            break;
            
            case "Membership Fee":
                $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                if ($count_txnID > 0) $txnID = "";

                $sum = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='alertpay'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);


//                sendMail ("alnik@rambler.ru", "test3", $count."___".$amount."___".$sum."___".$txnID."___".$security_code2."___".$security_code, $this->emailHeader);

                if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Success" And $security_code2 == $security_code)
                {
                    $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$member_id'");
                    $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                    if ($enr_level == 0)
                    {
                        $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                        $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$member_id'");
                    }


                    $thisTime = time ();
                    payUpline ($member_id, $txnID, $item_level, '3');
                    $subject = "Member payment report";
                    $message = "Member ID=$member_id made a payment";
                    sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                }
                else
                {

                    $subject = "Mistake payment report";
                    $message = "Member ID=$member_id attempted to make a payment\r\n";
                    $message .= "AlertPay returned the next variables:\r\n";
                    $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                    PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
//                    sendMail ("alnik@rambler.ru", "test4", $testMail, $this->emailHeader);
                    sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                }
            break;

            case "Payment_for_level":
                $count_txnID = $this->db->GetOne ("Select Count(*) From `payins` Where transaction_id='$txnID'", 0);
                if ($count_txnID > 0) $txnID = "";

                $sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='alertpay'", "0.00");
                $sum = $sum + $sum / 100 * $processor_fee;
                $sum = sprintf ("%01.2f", $sum);
                $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);


//                sendMail ("alnik@rambler.ru", "test3", $count."___".$amount."___".$sum."___".$txnID."___".$security_code2."___".$security_code, $this->emailHeader);

                if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Success" And $security_code2 == $security_code)
                {

                    $thisTime = time ();
                    payUpline ($member_id, $txnID, $item_level, '3');
                    $subject = "Member payment report";
                    $message = "Member ID=$member_id made a payment";
                    sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                }
                else
                {

                    $subject = "Mistake payment report";
                    $message = "Member ID=$member_id attempted to make a payment\r\n";
                    $message .= "AlertPay returned the next variables:\r\n";
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

                $subject = "A mistake message from".$siteTitle;
                $message = $descr."\r\n";
                $message .= "Member ID=$member_id attempted to make a payment\r\n";
                $message .= "AlertPay returned the next variables:\r\n";
                $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr, PAYMENT_BATCH_NUM(transaction_id)=$txnID,  Test Email: $testMail";
                sendMail ($siteEmail, $subject, $message, $this->emailHeader);

        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("alertpay");

$zPage->RunController ();

?>
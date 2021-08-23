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
        
        $header = $emailtext = ""; 
        
        $testMail = "";
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
        }

        // Read the post from OKPAY and add 'ok_verify' 
        $req = 'ok_verify=true'; 

        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
        
        $header .= "POST /ipn-verify.html HTTP/1.0\r\n"; 
        $header .= "Host: www.okpay.com\r\n"; 
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
        $fp = fsockopen ('www.okpay.com', 80, $errno, $errstr, 30); 
        
        $siteEmail = $this->db->GetSetting ("ContactEmail");
        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $cycling = $this->db->GetSetting ("cycling", 0);
        
        if (!$fp) 
        {
            sendMail ($siteEmail, "$siteTitle HTTP error", $testMail, $this->emailHeader);
        } 
        else
        {
            // NO HTTP ERROR  
            fputs ($fp, $header . $req); 
            while (!feof($fp))
            { 
                $res = fgets ($fp, 1024); 
                if (strcmp ($res, "VERIFIED") == 0)
                { 
                    // TODO: 
                    // Check the "ok_txn_status" is "completed"
                    
                    $ok_txn_status = $_POST ["ok_txn_status"];
                    $txnID = $_POST ["ok_txn_id"];
                    $ok_receiver_email = $_POST ["ok_receiver_email"];
                    $amount = $_POST ["ok_txn_gross"];
                    $ok_txn_currency = $_POST ["ok_txn_currency"];
                    $ok_item_1_name = $_POST ["ok_item_1_name"];
                    
/*
[ok_item_1_custom_1_title] = member_id
[ok_item_1_custom_1_value] = 1
[ok_item_1_custom_2_title] = level_id
[ok_item_1_custom_2_value] = 1.00
completed , 0 == 0 And admin@adailycash.com == admin@adailycash.com And 1.00 == 1.00 And USD, USD
*/                    
                    
                    $member_id = $_POST ["ok_item_1_custom_1_value"];
                    $item_level = $_POST ["ok_item_1_custom_2_value"];
                    $descr = $_POST ["ok_item_1_custom_3_value"];
                    
                    $processor = $this->db->GetEntry ("Select * From `processors` Where code='okpay'");
                    $processor_fee = $processor ["fee"];
                    $processor_account = $processor ["account_id"];
                    
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

                        if ($ok_txn_status == "completed" And $txnID != "" And $ok_receiver_email == $processor_account And $ok_txn_currency == "EURO" And $amount == $sum)
                        {
                            payUpline ($member_id, $txnID, $item_level, '6');
                            $subject = "Member payment report";
                            $message = "Member ID=$member_id made a OkPay payment";
                            sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                        }
                        else
                        {
  
                            $subject = "Mistake payment report";
                            $message = "Member ID=$member_id attempted to make a OkPay payment\r\n";
                            $message .= "OkPay returned the next variables:\r\n";
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

                        if ($ok_txn_status == "completed" And $txnID != "" And $ok_receiver_email == $processor_account And $ok_txn_currency == "EURO" And $amount == $sum)
                        {
                            payUpline ($member_id, $txnID, $item_level, '6');
                            $subject = "Member payment report";
                            $message = "Member ID=$member_id made a OkPay payment";
                            sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                        }
                        else
                        {
  
                            $subject = "Mistake payment report";
                            $message = "Member ID=$member_id attempted to make a OkPay payment\r\n";
                            $message .= "OkPay returned the next variables:\r\n";
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


                        if ($ok_txn_status == "completed" And $txnID != "" And $ok_receiver_email == $processor_account And $ok_txn_currency == "EURO" And $amount == $sum)
                        {
                            payProduct ($member_id, $txnID, $item_level, '6');
                            $subject = "Member product payment report";
                            $message = "Member ID=$member_id made a OkPay product payment";
                            sendMail ($siteEmail, $subject, $message, $this->emailHeader);
                        }
                        else
                        {

                            $subject = "Mistake product payment report";
                            $message = "Member ID=$member_id attempted to make a OkPay product payment\r\n";
                            $message .= "OkPay returned the next variables:\r\n";
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
                else if (strcmp ($res, "INVALID") == 0)
                { 
                    // If 'INVALID', send an email. TODO: Log for manual investigation. 
                    foreach ($_POST as $key => $value)
                    { 
                        $emailtext .= $key . " = " .$value ."\n\n"; 
                    } 
                    sendMail ($siteEmail, "Wrong Payment 1", $emailtext, $this->emailHeader); 
                } 
                else if (strcmp ($res, "TEST")== 0)
                {
	                 sendMail ($siteEmail, "Wrong Payment 2", "Test payment", $this->emailHeader);  
                }
            } 
            fclose ($fp); 
        }
        
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("okpay");

$zPage->RunController ();

?>
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
        $date_now = time ();

        if ( !empty($_POST) ) $this->db->ExecuteSql("insert into payins_log (data,processor) values('".json_encode($_POST)."','paypal') ");


        $data = $this->db->GetOne ("Select data From payins_log Where id=1");
        $data = json_decode($data, true);
//debug($data);
        $error = "";
        $txn_type = $this->GetGP ("txn_type", -1) ;
        $adminEmail = $this->db->GetOne ("Select value From settings Where keyname='ContactEmail'");

        $testMail = "";
        $postipn = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
            $value = urlencode(stripslashes($value));
            $postipn .= "&$key=$value";
        }
//        sendMail ("alexk@speedster-it.com", "It works!!!", $testMail, $this->emailHeader);

        $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postipn);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        //if ($this->use_local_certs) curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");

        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: PHP-IPN-Verification-Script',
            'Connection: Close',
        ));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            throw new Exception("PayPal responded with http code $http_code");
        }
        curl_close($ch);

        debug($res);



        // post back to PayPal system to validate
        $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: ".strlen($postipn)."\r\n\r\n";
        $port = fsockopen ("ipnpb.sandbox.paypal.com", 80, $errno, $errstr, 30);

        if (!$port) {
            // HTTP ERROR
            $error .= "Error of open port\r\n";
//            sendMail ("alexk@speedster-it.com", "It doesn't work!!!", $testMail, $this->emailHeader);
        }
        else
        {
            $res = fputs ($port, $header.$postipn);

//            sendMail ("alexk@speedster-it.com", "It works - 2!!!", $testMail, $this->emailHeader);

            while (!feof ($port))
            {
                $reply = fgets ($port, 1024);
                $reply = trim ($reply);
            }
            debug($reply);
            // IPN was Confirmed as both Genuine and VERIFIED
            if (!strcmp ($reply, "VERIFIED"))
            {

//                sendMail ("alexk@speedster-it.com", "It works - 3!!!", $testMail, $this->emailHeader);

                //Now that IPN was VERIFIED below are a few things which you may want to do at this point.
                //1. Check that the "payment_status" variable is: "Completed"
                //2. If it is Pending you may want to wait or inform your customer?
                //3. You should Check your datebase to ensure this "txn_id" or "subscr_id" is not a duplicate. txn_id is not sent with subscriptions!
                //4. Check "payment_gross" or "mc_gross" matches match your prices!
                //5. You definately want to check the "receiver_email" or "business" is yours.
                if ($txn_type != -1)
                {
                    $txnID = $this->GetGP ("txn_id");
                    $count = $this->db->GetOne ("Select Count(*) from `payins` where transaction_id='$txnID'", -1);
                    if ($count > 0)
                    {
                        $txnID = "";
                    }

//                    sendMail ("alexk@speedster-it.com", "It works - 4!!!", $txnID, $this->emailHeader);


                    // get total prices from DB
                    $member_id = $this->GetGP ("item_number"); //member id
                    $status = $this->GetGP ("payment_status"); //Completed
                    $descr = $this->GetGP ("item_name"); //Payment_for_eBook
                    $amount = $this->GetGP("mc_gross");//amount payment_gross
                    $payer_id = $this->GetGP ("payer_email");//payer ID
                    $receiver_email = $this->GetGP ("receiver_email"); //receiver ID
                    $item_level = $this->GetGP ("custom"); //m_lemel (2)

                    switch ($descr)
                    {

                        case "Payment_for_level":
                            $sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
                            $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='paypal'", "0.00");
                            $sum = $sum + $sum / 100 * $processor_fee;
                            $sum = sprintf ("%01.2f", $sum);
                            $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                            if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Completed")
                            {

                                $thisTime = time ();
                                payUpline ($member_id, $txnID, $item_level, '8');
                                $subject = "Member payment report";
                                $message = "Member ID=$member_id made a PayPal payment";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                            }
                            else
                            {

                                $subject = "Mistake payment report";
                                $message = "Member ID=$member_id attempted to make a payment\r\n";
                                $message .= "Pay Pal returned the next variables:\r\n";
                                $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                                PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                            }
                            break;

                        case "Membership Fee":
                            $sum = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
                            $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='paypal'", "0.00");
                            $sum = $sum + $sum / 100 * $processor_fee;
                            $sum = sprintf ("%01.2f", $sum);
                            $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                            if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Completed")
                            {

                                $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$member_id'");
                                $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                                if ($enr_level == 0)
                                {
                                    $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                                    $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$member_id'");
                                }

                                $thisTime = time ();
                                payUpline ($member_id, $txnID, $item_level, '8');
                                $subject = "Member payment report";
                                $message = "Member ID=$member_id made a PayPal payment";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                            }
                            else
                            {

                                $subject = "Mistake payment report";
                                $message = "Member ID=$member_id attempted to make a payment\r\n";
                                $message .= "Pay Pal returned the next variables:\r\n";
                                $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                                PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                            }
                            break;

                        case "Product Payment":

                            $sum = $this->db->GetOne ("Select price From `products` Where product_id='$item_level'");
                            $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='paypal'", "0.00");
                            $sum = $sum + $sum / 100 * $processor_fee;
                            $sum = sprintf ("%01.2f", $sum);
                            $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                            if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Completed")
                            {

                                $thisTime = time ();
                                payProduct ($member_id, $txnID, $item_level, '8');

                                $subject = "Member product payment report";
                                $message = "Member ID=$member_id made a product payment";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                            }
                            else
                            {

                                $subject = "Mistake product payment report";
                                $message = "Member ID=$member_id attempted to make a product payment\r\n";
                                $message .= "Pay Pal returned the next variables:\r\n";
                                $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                                PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                                sendMail ($adminEmail, $subject, $message, $this->emailHeader);
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

                    }
                }
            }
            // IPN was Not Validated as Genuine and is INVALID
            elseif (!strcmp ($reply, "INVALID"))
            {
                $error .= "Error of reply. Invalid\r\n";

                //Now that IPN was INVALID below are a few things which you may want to do at this point.
                //1. Check your code for any post back Validation problems!
                //2. Investigate the Fact that this Could be an attack on your script IPN!
                //3. If updating your DB, Ensure this "txn_id" is Not a Duplicate!

//                sendMail ("alexk@speedster-it.com", "It doesn't work - 7!!!", $testMail, $this->emailHeader);

            }
            else
            {
//                sendMail ("alexk@speedster-it.com", "It doesn't work - 8!!!", $testMail, $this->emailHeader);
                $error .= "Error of reply. None\r\n";
            }

        }
        fclose ($port);
        header ("Status: 200 OK");
        exit ();

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("PayPal");

$zPage->RunController ();

?>
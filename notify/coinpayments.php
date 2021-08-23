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
        $adminEmail = $this->db->GetOne ("Select value From settings Where keyname='ContactEmail'");

        $testMail = "";
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
        }
        $this->db->ExecuteSql("Insert Into `payins_log` (`data`, `processor`, `date`) Values ('".json_encode($_POST)."', 'coinpayments', 'NOW()')");
        //sendMail ("me@irus.pro", "It works!!!", json_encode($_POST), $this->emailHeader);

        $code = 'coinpayments';
        $processorData = $this->db->GetEntry("Select * From `processors` Where code='$code'");
        $codeId = $processorData['processor_id'];

        // Fill these in with the information from your CoinPayments.net account.
        $cp_merchant_id = $processorData['account_id'];
        $cp_ipn_secret = $processorData['extra_field'];
        $cp_debug_email = '';

        //These would normally be loaded from your database, the most common way is to pass the Order ID through the 'custom' POST field.
        $order_currency = $this->currency_name;

        function errorAndDie($error_msg) {
            global $cp_debug_email;
            if (!empty($cp_debug_email)) {
                $report = 'Error: '.$error_msg."\n\n";
                $report .= "POST Data\n\n";
                foreach ($_POST as $k => $v) {
                    $report .= "|$k| = |$v|\n";
                }
                //mail($cp_debug_email, 'CoinPayments IPN Error', $report);
            }
            die('IPN Error: '.$error_msg);
        }

        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            errorAndDie('IPN Mode is not HMAC');
        }

        //sendMail ("me@irus.pro", "It works!!!", $_SERVER['HTTP_HMAC'], $this->emailHeader);

        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            errorAndDie('No HMAC signature sent.');
        }

        $request = file_get_contents('php://input');
        if ($request === FALSE || empty($request)) {
            errorAndDie('Error reading POST data');
        }

        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
            errorAndDie('No or incorrect Merchant ID passed');
        }

        $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            //if ($hmac != $_SERVER['HTTP_HMAC']) { <-- Use this if you are running a version of PHP below 5.6.0 without the hash_equals function
            errorAndDie('HMAC signature does not match');
        }

        // HMAC Signature verified at this point, load some variables.
        $txnID = $_POST['txn_id'];
        //$item_name = $_POST['item_name'];
        //$item_number = $_POST['item_number'];
        //$amount1 = floatval($_POST['amount1']);
        //$amount2 = floatval($_POST['amount2']);
        $currency1 = $_POST['currency1'];
        //$currency2 = $_POST['currency2'];
        $status = intval($_POST['status']);
        //$status_text = $_POST['status_text'];

        //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point

        // Check the original currency to make sure the buyer didn't change it.
        if ($currency1 != $order_currency) {
            errorAndDie('Original currency mismatch!');
        }

        //sendMail ("me@irus.pro", "It works!!!", 'point1', $this->emailHeader);

        if ($status >= 100 || $status == 2) {
            // payment is complete or queued for nightly payout, success
            $count = $this->db->GetOne ("Select Count(*) from `payins` where transaction_id='$txnID'", -1);
            if ($count > 0) $txnID = "";

            // get total prices from DB
            $member_id = $this->GetGP ("invoice"); //member id
            $descr = $this->GetGP ("item_name"); //Payment_for_eBook
            $amount = $this->GetGP("amount1");//amount payment_gross
            $item_level = $this->GetGP ("custom"); //m_lemel (2)

            switch ($descr)
            {
                case "Payment_for_level":
                    $sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
                    $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='$code'", "0.00");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);
                    //sendMail ("me@irus.pro", "It works!!!", "$count == 1 And $amount == $sum And $txnID != \"\"", $this->emailHeader);
                    if ($count == 1 And $amount == $sum And $txnID != "" )
                    {

                        $thisTime = time ();
                        payUpline ($member_id, $txnID, $item_level, $codeId);
                        $subject = "Member payment report";
                        $message = "Member ID=$member_id made a $code payment";
                        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {

                        $subject = "Mistake payment report";
                        $message = "Member ID=$member_id attempted to make a payment\r\n";
                        $message .= "$code returned the next variables:\r\n";
                        $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                                PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                    }
                    break;

                case "Membership Fee":
                    $sum = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
                    $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='$code'", "0.00");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                    if ($count == 1 And $amount == $sum And $txnID != "" )
                    {

                        $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$member_id'");
                        $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                        if ($enr_level == 0)
                        {
                            $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                            $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$member_id'");
                        }

                        $thisTime = time ();
                        payUpline ($member_id, $txnID, $item_level, $codeId);
                        $subject = "Member payment report";
                        $message = "Member ID=$member_id made a $code payment";
                        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {

                        $subject = "Mistake payment report";
                        $message = "Member ID=$member_id attempted to make a payment\r\n";
                        $message .= "$code returned the next variables:\r\n";
                        $message .= "PAYMENT_AMOUNT(summ)=$amount, DESCRIPTION(description)=$descr,
                                PAYMENT_BATCH_NUM(transaction_id)=$txnID, Test Email: $testMail";
                        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                    }
                    break;

                case "Product Payment":

                    $sum = $this->db->GetOne ("Select price From `products` Where product_id='$item_level'");
                    $processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='$codeId'", "0.00");
                    $sum = $sum + $sum / 100 * $processor_fee;
                    $sum = sprintf ("%01.2f", $sum);
                    $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

                    if ($count == 1 And $amount == $sum And $txnID != "" )
                    {

                        $thisTime = time ();
                        payProduct ($member_id, $txnID, $item_level, $codeId);

                        $subject = "Member product payment report";
                        $message = "Member ID=$member_id made a product payment";
                        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
                    }
                    else
                    {

                        $subject = "Mistake product payment report";
                        $message = "Member ID=$member_id attempted to make a product payment\r\n";
                        $message .= "$code returned the next variables:\r\n";
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


        } else if ($status < 0) {
            //payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent
        } else {
            //payment is pending, you can optionally add a note to the order page
        }
        exit ('IPN OK');

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("coinpayments");

$zPage->RunController ();

?>
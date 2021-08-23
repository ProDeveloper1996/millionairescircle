<?php
require_once ("../includes/config.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");
require_once ("EgoPaySci.php");
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
		$error = "";
        $txn_type = $this->GetGP ("product_id", "") ;
		//$txn_type = 'BASGRYFX4LNZ';
        $adminEmail = $this->db->GetOne ("Select value From settings Where keyname='ContactEmail'");

        $testMail = "";
        foreach ($_POST as $key => $value) {
            $testMail .= "[$key] = $value \r\n";
        }
        //sendMail ("kovchinskiy@gmail.com", "It works!!!".$txn_type, $testMail, $this->emailHeader);
		if($txn_type!='')
		{
			try
			{
				$store_id=$this->db->GetOne ("Select `account_id` From `processors` Where code='egopay'", "");
				$callbackHandler = new EgoPaySciCallback(array(
					'store_id'          => $store_id,
					'store_password'    => 'vrQgCinUAA0FXcgMe6099RdoX4JGCf3p',
					'checksum_key'      => 'HwIJORXNagIjsnPMYr0wwJslQYLUf8vT',
				));
				//$array = array("product_id" => $txn_type);
				//$response = $callbackHandler->getResponse($array);
				$response = $callbackHandler->getResponse($_POST);
				//print_r($response);
				if (!empty($response['sStatus']))
				{
					switch($response['sStatus'])
					{
						case 'Completed':
						case 'Successful':
						case 'TEST SUCCESS':
						{
							//sendMail ("alexk@speedster-it.com", "It works - 3!!!", $testMail, $this->emailHeader);

							//Now that IPN was VERIFIED below are a few things which you may want to do at this point.
							//1. Check that the "payment_status" variable is: "Completed"
							//2. If it is Pending you may want to wait or inform your customer?
							//3. You should Check your datebase to ensure this "txn_id" or "subscr_id" is not a duplicate. txn_id is not sent with subscriptions!
							//4. Check "payment_gross" or "mc_gross" matches match your prices!
							//5. You definately want to check the "receiver_email" or "business" is yours.
							$member_id = $response['cf_1']; //member id
							$status = $response['sStatus']; //Completed
							$status='Completed';
							$descr = $response['sDetails']; //Payment_for_eBook
							$amount = $response['fAmount'];//amount
							$item_level = $response['cf_2'];//m_lemel (2)
							$sDate = $response['sDate'];//date
							$txnID = $response['sId'];//tranzaction
							$sCurrency = $response['sCurrency'];//currency
							$fFee = $response['fFee'];//fee
							
							$count = $this->db->GetOne ("Select Count(*) from `payins` where transaction_id='$txnID'", -1);
							if ($count > 0)
							{
								$txnID = "";
							}
							
							switch ($descr)
							{
								
								case "Payment_for_level":
									$sum = $this->db->GetOne ("Select cost From `types` Where order_index='$item_level'");
									$processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='egopay'", "0.00");
									$sum = $sum + $sum / 100 * $processor_fee;
									$sum = sprintf ("%01.2f", $sum);
									$count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

									if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Completed")
									{

										$thisTime = time ();
										payUpline ($member_id, $txnID, $item_level, '9');
										$subject = "Member payment report";
										$message = "Member ID=$member_id made a EgoPay payment";
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
									$processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='egopay'", "0.00");
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
										payUpline ($member_id, $txnID, $item_level, '9');
										$subject = "Member payment report";
										$message = "Member ID=$member_id made a EgoPay payment";
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
									$processor_fee = $this->db->GetOne ("Select fee From `processors` Where code='egopay'", "0.00");
									$sum = $sum + $sum / 100 * $processor_fee;
									$sum = sprintf ("%01.2f", $sum);
									$count = $this->db->GetOne ("Select Count(*) From members Where member_id='$member_id'", 0);

									if ($count == 1 And $amount == $sum And $txnID != "" And $status == "Completed")
									{

										$thisTime = time ();
										payProduct ($member_id, $txnID, $item_level, '9');
										
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
							break;
						}
						case 'Pending':
						{
							$error .= "Error of reply. Invalid\r\n";
							break;
						}
						case 'On Hold':
						{
							$error .= "Error of reply. Invalid\r\n";
							break;
						}
						default:
						{
							$error .= "Error of reply. Invalid\r\n";
							//Now that IPN was INVALID below are a few things which you may want to do at this point.
							//1. Check your code for any post back Validation problems!
							//2. Investigate the Fact that this Could be an attack on your script IPN!
							//3. If updating your DB, Ensure this "txn_id" is Not a Duplicate!
							break;
						}
					}

				}
				else
				{
					echo 'error';
					die;
				}
				//@todo: check if order amount and currency is valid
				//@todo: check your order status
				//@todo: update your database
			} catch(EgoPayException $e) {
				die($e->getMessage());
			}
		}
        header ("Status: 200 OK");
        exit ();
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("EgoPay");

$zPage->RunController ();

?>

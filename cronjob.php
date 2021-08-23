<?php
// /usr/bin/wget -O - -q -t 1 '25dollarlegacy.com/cronjob.php'

if (!array_key_exists ("argv", $_SERVER)) exit ("This script cannot be executed in this mode. Please check documentations for more information.");
$path_to_site = $_SERVER['argv'][1];
if (substr ($path_to_site, -1) == "/") $path_to_site = substr ($path_to_site, 0, -1);

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_admin.php");
require_once ("./includes/utilities.php");

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
        if ( statusAfterLaunch() == 1 || isPreLaunch() ) return ;

        //preparing cronjob
        $PM = $this->db->GetSetting ("PaymentMode");
        $cycling = $this->db->GetSetting ("cycling");
        $lastCronjobStart = $this->db->GetSetting ("LastCronjobStart");
        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $siteUrl = $this->db->GetSetting ("siteUrl");
        $adminEmail = $this->db->GetSetting ("ContactEmail");

        $payPeriod = $this->db->GetSetting ("payPeriod");
        $warnPeriod = $this->db->GetSetting ("warnPeriod");
        $monthPeriod = $this->db->GetSetting ("monthPeriod");
        $turn_date = $this->db->GetSetting ("PaymentModeDate");

        $thisTime = time ();
        $thisMonth = mktime (0, 0, 0, date ("m"), 0, date ("Y"));
        $startToday = mktime (0, 0, 0, date("m")  , date("d"), date("Y"));
        $finishToday = mktime (23, 59, 59, date("m")  , date("d"), date("Y"));
        $report = "";

        //clearing online_stats table
        $this->db->ExecuteSql ("Truncate Table online_stats");

        $useAutoresponder = $this->db->GetSetting ("useAutoresponder", 0);
        if ($useAutoresponder == 1)
        {

            //Sending Free members Autoresponder emails
            $report .= "\r\n Free Members Autoresponder emails have been sent:\r\n";
            $sql = ($cycling == 1)? " `m_level`=0 " : " `m_level`<2 ";
            $result = $this->db->ExecuteSql ("Select * From `members` Where `is_active`=1 And `is_dead`=0 And $sql");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row["member_id"];
                $reg_date = $row["reg_date"];
            
                $regDay = mktime (0, 0, 0, date("m", $reg_date)  , date("d", $reg_date), date("Y", $reg_date));
                        
                $daysAgo = ($startToday - $regDay) / 86400;
            
                $subject = $this->dec ($this->db->GetOne ("Select `subject` From `autoresponders` Where `z_day`='$daysAgo' And `is_active`=1 And `is_free`=1", ""));
                if ($subject != "")
                {
                    $message = $this->dec ($this->db->GetOne ("Select `message` From `autoresponders` Where `z_day`='$daysAgo' And `is_free`=1", ""));
                
                    $subject = preg_replace ("/\[SiteTitle\]/", $siteTitle, $subject);
                        
                    $message = preg_replace ("/\[SiteTitle\]/", $siteTitle, $message);
                    $message = preg_replace ("/\[SiteUrl\]/", $siteUrl, $message);
                    $message = preg_replace ("/\[FirstName\]/", $this->dec ($row ["first_name"]), $message);
                    $message = preg_replace ("/\[LastName\]/", $this->dec ($row ["last_name"]), $message);
                    $message = preg_replace ("/\[MemberUsername\]/", $row ["username"], $message);
                    $message = preg_replace ("/\[MemberID\]/", $row ["member_id"], $message);
                    $message = preg_replace ("/\[SponsorID\]/", $row ["enroller_id"], $message);
                
                    sendMail ($row ["email"], $subject, $message, $this->emailHeader);                
                
                    $report .= "Free Members Autoresponder email has been sent to Member #$id ($daysAgo day).\r\n";
                }
            
            
            }
            $this->db->FreeSqlResult ($result);
            
            
            //Sending Paid members Autoresponder emails
            $report .= "\r\n Paid Members Autoresponder emails have been sent:\r\n";
            $sql = ($cycling == 1)? " `m_level`>0 " : " `m_level`>1 ";
            $result = $this->db->ExecuteSql ("Select * From `members` Where `is_active`=1 And `is_dead`=0 And $sql");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row["member_id"];

            	$first_payment = $this->db->GetOne ("Select MIN(z_date) From `payins` Where `member_id`='$id' And `product_id`=0", 0); 
                $regDay = mktime (0, 0, 0, date("m", $first_payment)  , date("d", $first_payment), date("Y", $first_payment));
                        
                $daysAgo = ($startToday - $regDay) / 86400;
            
                $subject = $this->dec ($this->db->GetOne ("Select `subject` From `autoresponders` Where `z_day`='$daysAgo' And `is_active`=1 And `is_free`=0", ""));
                if ($subject != "")
                {
                    $message = $this->dec ($this->db->GetOne ("Select `message` From `autoresponders` Where `z_day`='$daysAgo' And `is_free`=0", ""));
                
                    $subject = preg_replace ("/\[SiteTitle\]/", $siteTitle, $subject);
                        
                    $message = preg_replace ("/\[SiteTitle\]/", $siteTitle, $message);
                    $message = preg_replace ("/\[SiteUrl\]/", $siteUrl, $message);
                    $message = preg_replace ("/\[FirstName\]/", $this->dec ($row ["first_name"]), $message);
                    $message = preg_replace ("/\[LastName\]/", $this->dec ($row ["last_name"]), $message);
                    $message = preg_replace ("/\[MemberUsername\]/", $row ["username"], $message);
                    $message = preg_replace ("/\[MemberID\]/", $row ["member_id"], $message);
                    $message = preg_replace ("/\[SponsorID\]/", $row ["enroller_id"], $message);
                
                    sendMail ($row ["email"], $subject, $message, $this->emailHeader);                
                
                    $report .= "Paid Members Autoresponder email has been sent to Member #$id ($daysAgo day).\r\n";
                }
            
            
            }
            $this->db->FreeSqlResult ($result);
            
        }
                

        // removing non-activated members
        $time_activation = 1 * 24 * 3600;
        $report .= "\r\nRemoved non-activated members:\r\n";
        $total_unact = $this->db->GetOne ("Select Count(*) From `members` Where (($thisTime - reg_date) > $time_activation) And is_active=0", 0);
        if ($total_unact > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `members` Where (($thisTime - reg_date) > $time_activation) And is_active=0");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row["member_id"];
                $this->db->ExecuteSql ("Delete From `members` Where member_id='$id'");
                $report .= "Member #$id was removed as non-activated.\r\n";
            }
            $this->db->FreeSqlResult ($result);
        }
		$total_unpaid = 0;
        if ($cycling == 0 And $PM == 1)
        {
            // removing non-payed members
            $report .= "\r\nRemoved non-paid members:\r\n";
            $total_unpaid = $this->db->GetOne ("Select Count(*) From `members` Where ($thisTime - (Greatest(reg_date, $turn_date) + quant_pay * $monthPeriod * 24 * 60 * 60) > ($payPeriod + $warnPeriod) * 24 * 60 * 60) And member_id>1", 0);
            if ($total_unpaid > 0)
            {
                $result = $this->db->ExecuteSql ("Select * From `members` Where ($thisTime - (Greatest(reg_date, $turn_date) + quant_pay * $monthPeriod * 24 * 60 * 60) > ($payPeriod + $warnPeriod) * 24 * 60 * 60) And member_id>1 Order By member_id Desc");
                while ($row = $this->db->FetchInArray ($result))
                {
                    $id = $row["member_id"];
                    $enr_id = $row["enroller_id"];

                    $this->db->ExecuteSql ("Delete From `payins` Where member_id='$id'");
                    $this->db->ExecuteSql ("Delete From `tickets` Where member_id='$id'");
                    $this->db->ExecuteSql ("Delete From `cash` Where to_id='$id'");
                    $this->db->ExecuteSql ("Delete From `cash_out` Where user_id='$id'");
                    $this->db->ExecuteSql ("Delete From `selected` Where member_id='$id'");
                    out_matrix ($id, $enr_id);
                    $this->db->ExecuteSql ("Delete From `members` Where member_id='$id'");
                    $report .= "Member #$id was removed.\r\n";
                }
                $this->db->FreeSqlResult ($result);
            }

            // a notification letter about the end of active period
            $total_stat = $this->db->GetOne ("Select Count(*) From `members` Where ((Greatest(reg_date, $turn_date) + (quant_pay * $monthPeriod * 24 * 3600) - $thisTime) < 1 * 24 * 3600) And ((Greatest(reg_date, $turn_date) + (quant_pay * $monthPeriod * 24 * 3600) - $thisTime) > 0)", 0);
            if ($total_stat > 0)
            {
                $result = $this->db->ExecuteSql ("Select * From `members` Where ((Greatest(reg_date, $turn_date) + (quant_pay * $monthPeriod * 24 * 3600) - $thisTime) < 1 * 24 * 3600) And ((Greatest(reg_date, $turn_date) + (quant_pay * $monthPeriod * 24 * 3600) - $thisTime) > 0)");
                $subject = "MLM Builder";
                while ($row = $this->db->FetchInArray ($result))
                {
                    $id = $row["member_id"];
                    $firstName = $row["first_name"];
                    $lastName = $row["last_name"];
                    $username = $row["username"];
                    $email = $row["email"];
                    
                    $row2 = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='17'", ""); 
                    if ($row2 ["is_active"] == 1)
                    {
                        $subject = $this->dec ($row2 ["subject"]);
                        $message = $this->dec ($row2 ["message"]);
                        $subject = preg_replace ("/\[SiteTitle\]/", $siteTitle, $subject);
                        
                        $message = preg_replace ("/\[SiteTitle\]/", $siteTitle, $message);
                        $message = preg_replace ("/\[FirstName\]/", $firstName, $message);
                        $message = preg_replace ("/\[LastName\]/", $lastName, $message);
                        $message = preg_replace ("/\[Username\]/", $username, $message);
                
                        sendMail ($email, $subject, $message, $this->emailHeader);
                    }
                }
                $this->db->FreeSqlResult ($result);
            }
        }

        $this->db->SetSetting ("LastCronjobStart", $thisTime);

        $report .= "Unpaid members removed : $total_unpaid.\r\n";
        $report .= "Inactivated members removed : $total_unact.\r\n";

        $mail_body = "Cron job was executed at ".date ("j F Y, g:i a")."\r\n\r\n$report\r\n".
        "---------------\r\n".
        "$siteTitle\r\n";
        sendMail ($adminEmail, "Cron job work results", $mail_body, $this->emailHeader);
//        sendMail ("alnik@rambler.ru", "Cron job work results", $mail_body, $this->emailHeader);
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cron");

$zPage->RunController ();

?>
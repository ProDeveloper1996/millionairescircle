<?php
require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->pageTitle="Send e-mail";
        $this->pageHeader="Send e-mail";
    }

    //_-----------------------------------------------------------------------------
    function ocd_list()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate="./templates/contact_all.tpl";
        $member_id = $this->member_id;
        $e = $this->GetGp ("s",0);
        $s = $this->GetGp ("e", $e);

        $mem_exit = array ();

        if ($s == 0)
        {

            $cycling = $this->db->GetSetting ("cycling", 0);
            if ($cycling == 1)
            {
                $count = $this->db->GetOne ("Select Count(distinct member_id) From `matrix` Where host_id='$member_id' And member_id>0", 0);
                if ($count > 0)
                {
                    $result = $this->db->ExecuteSql ("Select distinct member_id From `matrix` Where host_id='$member_id' And member_id>0");
                    while ($row = $this->db->FetchInArray ($result))
                    {
                        $mem_exit [] = $row ["member_id"];
                    }
                }
            }
            else
            {
                $downline = array ();
                $mem_exit = getNumberDownlines ($member_id, $downline);
            }
        }
        else
        {
            if ($this->GetGp ("s",0)>0)
                $isExist = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$member_id' And member_id=$s ", 0);
            if ($this->GetGp ("e",0)>0)
                $isExist = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$s' And member_id=$member_id ", 0);
            if ( $isExist==0 )  $this->Redirect('/member/myaccount.php');//$this->pageUrl."?m=nom&s=$s"
            $mem_exit [] = $s;
        }
        sort ($mem_exit);
        $content = "";
        $k = 1;

        foreach ($mem_exit as $a)
        {
            $first_name = $this->db->GetOne("Select first_name From members Where member_id='$a'");
            $last_name = $this->db->GetOne("Select last_name From members Where member_id='$a'");
            $sponsor_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$a'", 0);
            $content .= ($sponsor_id == $member_id)? $k.". <b>".$first_name." ".$last_name."</b>(ID=".$a.").<br>" : $k.". ".$first_name." ".$last_name."(ID=".$a.").<br>";
            $k++;
        }

        $this->SaveStateValue ("mem_exit", $mem_exit);
        $this->SaveStateValue ("s", $s);

        $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='9'", "");

        
        
        $subject = $this->dec($row ["subject"]);
        $message = $this->dec($row ["message"]);
        $ch_templ = $this->dec($row ["tag_descr"]);

        $e_subject = "<input type='text' name='subject' value='$subject' maxlength='250' style='width:450px;'>";
        $e_message = "<textarea name='message' rows='15' style='width:450px;'>$message</textarea>";

        $thanks = "";

        $m = $this->GetGP ("m");
        $let = $this->GetGP ("let");
        $thanks = ($m == "ok")? "Your message has been successfully sent to $let member(s)." : $thanks;
        $thanks = ($m == "no")? "You cannot send email." : $thanks;
        $thanks = ($m == "nom")? "Nu such members." : $thanks;

        $choose = "";
        if (Count ($mem_exit) > 1)
        {
            $choose = "<input type='radio' name='dest' value='1' checked> Send to all of them <input type='radio' name='dest' value='2'> Send only to sponsored <input type='radio' name='dest' value='3'> Send to joined in the last <input type='text' name='days' value='' maxlength='3' style='width: 30px;'> days";
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "EMAIL_SUBJECT" => $e_subject,
            "EMAIL_MESSAGE" => $e_message,
            "CHANGE_TEMPLATE" => $ch_templ,
            "CONTENT" => $content,
            "THANKS" => $thanks,
            "CHOOSE" => $choose,
            );
    }
    //--------------------------------------------------------------------------
    function ocd_send_email ()
    {
        $sid = $this->member_id;

        $subject = $this->GetGp ('subject');
        $message = $this->GetGp ('message');
        $SiteTitle = $this->db->GetSetting ("SiteTitle");

        $semail = $this->db->GetOne("Select email From members Where member_id='$sid'");
        $sfirst_name = $this->db->GetOne("Select first_name From members Where member_id='$sid'");
        $slast_name = $this->db->GetOne("Select last_name From members Where member_id='$sid'");

        $message = preg_replace ("/\[SenderFirstName\]/", $sfirst_name, $message);
        $message = preg_replace ("/\[SenderLastName\]/", $slast_name, $message);
        $message = preg_replace ("/\[SenderID\]/", $sid, $message);
        $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);

        $subject = preg_replace ("/\[SenderFirstName\]/", $sfirst_name, $subject);
        $subject = preg_replace ("/\[SenderLastName\]/", $slast_name, $subject);
        $subject = preg_replace ("/\[SenderID\]/", $sid, $subject);
        $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
        
        $headers = "From: ".$semail."\r\n";
        $mem_exit = $this->GetStateValue ("mem_exit");
        $s = $this->GetStateValue ("s");

        $choose = $this->GetGP("dest", 1);
        $let = 0;

        switch ($choose)
        {
            case 1:

                foreach ($mem_exit as $each)
                {
                    $message_to = $message;
                    $email = $this->db->GetOne ("Select email From members Where member_id='$each'");
                    $firstname = $this->db->GetOne ("Select first_name From members Where member_id='$each'");
                    $lastname = $this->db->GetOne ("Select last_name From members Where member_id='$each'");
                    $message_to = preg_replace ("/\[FirstName\]/", $firstname, $message_to);
                    $message_to = preg_replace ("/\[LastName\]/", $lastname, $message_to);
                    $message_to = preg_replace ("/\[ID\]/", $each, $message_to);
                    $let++;
                    sendMail ($email, $subject, $message_to, $headers);
                    
                }
            break;
            case 2:

                foreach ($mem_exit as $each)
                {
                    $sponsor_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$each'");
                    if ($sponsor_id == $sid)
                    {
                        $message_to = $message;
                        $email = $this->db->GetOne ("Select email From members Where member_id='$each'");
                        $firstname = $this->db->GetOne ("Select first_name From members Where member_id='$each'");
                        $lastname = $this->db->GetOne ("Select last_name From members Where member_id='$each'");
                        $message_to = preg_replace ("/\[FirstName\]/", $firstname, $message_to);
                        $message_to = preg_replace ("/\[LastName\]/", $lastname, $message_to);
                        $message_to = preg_replace ("/\[ID\]/", $each, $message_to);
                        $let++;
                        sendMail ($email, $subject, $message_to, $headers);
                    }
                }

                if ($let == 0) $this->Redirect ($this->pageUrl."?m=nom&s=$s");

            break;
            case 3:
                $days = $this->GetGP ("days", 0);
                $days = trim (ereg_replace("[^0-9]", "", $days));
                $thisTime = time ();
                if (is_numeric ($days) And $days > 0)
                {
                    foreach ($mem_exit as $each)
                    {

                        $reg_date = $this->db->GetOne ("Select reg_date From `members` Where member_id='$each'");
                        if ($thisTime - $reg_date < $days * 24 * 3600)
                        {

                            $message_to = $message;
                            $email = $this->db->GetOne ("Select email From members Where member_id='$each'");
                            $firstname = $this->db->GetOne ("Select first_name From members Where member_id='$each'");
                            $lastname = $this->db->GetOne ("Select last_name From members Where member_id='$each'");
                            $message_to = preg_replace ("/\[FirstName\]/", $firstname, $message_to);
                            $message_to = preg_replace ("/\[LastName\]/", $lastname, $message_to);
                            $message_to = preg_replace ("/\[ID\]/", $each, $message_to);
                            $let++;
                            sendMail ($email, $subject, $message_to, $headers);
                        }
                    }
                    if ($let == 0) $this->Redirect ($this->pageUrl."?m=nom&s=$s");

                }
                else
                {
                    $this->Redirect ($this->pageUrl."?m=no&s=$s");
                }
            break;
            default:
                foreach ($mem_exit as $each)
                {
                    $message_to = $message;
                    $email = $this->db->GetOne ("Select email From members Where member_id='$each'");
                    $firstname = $this->db->GetOne ("Select first_name From members Where member_id='$each'");
                    $lastname = $this->db->GetOne ("Select last_name From members Where member_id='$each'");
                    $message_to = preg_replace ("/\[FirstName\]/", $firstname, $message_to);
                    $message_to = preg_replace ("/\[LastName\]/", $lastname, $message_to);
                    $message_to = preg_replace ("/\[ID\]/", $each, $message_to);
                    $let++;
                    sendMail ($email, $subject, $message_to, $headers);
                }
        }


        $this->Redirect ($this->pageUrl."?m=ok&s=$s&let=$let");
    }

    //_-----------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_
        <script type="text/javascript" language="JavaScript">
        <!--
        function validateForm (theForm)
        {

            if (theForm.message.value.length < 2) {
                alert ("Message is empty!");
                theForm.message.focus ();
                return false;
            }
            return true;
        }
        function insertText (text)
        {
            var taField = document.form1.message;
            //IE support
            if (document.selection)
            {
                taField.focus();
                sel = document.selection.createRange ();
                sel.text = text;
            }
        //MOZILLA/NETSCAPE support
            else if (taField.selectionStart || taField.selectionStart == '0')
            {
                var startPos = taField.selectionStart;
                var endPos = taField.selectionEnd;
                taField.value = taField.value.substring (0, startPos) + text + taField.value.substring (endPos, taField.value.length);
            } else
            {
                taField.value += text;
            }
        }


        -->
        </script>
_ENDOFJS_;
    }
}
//_-----------------------------------------------------------------------------

$zPage = new ZPage ("contact");
$zPage->Render ();
?>
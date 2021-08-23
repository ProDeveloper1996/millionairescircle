<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object = "none")
    {
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
        
		  $this->mainTemplate = "./templates/forgot_password.tpl";
        $this->pageTitle = $dict['FP_ForgotPassword'];
        $this->pageHeader = $dict['FP_ForgotPassword'];
        $email = "<input class='form-control' type='text' name='email' value='".$this->GetGP ("email")."' maxlength='120' style='width:160px;'>";
        $message = "<span class='error'>".$this->GetError ("email")."</span>";
        if ($this->GetGP ("ec") == "done") $message = "<span class='message'>{$dict['FP_Text1']}</span>";
        if ($this->GetGP ("ec") == "fail") $message = "<span class='error'>{$dict['FP_Text2']}</span>";
        if ($this->GetGP ("ec") == "wr") $message = "<span class='error'>{$dict['FP_Text3']}</span>";
        if ($this->GetGP ("ec") == "no") $message = "<span class='error'>{$dict['FP_Text4']}</span>";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "PAGE_ACTION" => $this->pageUrl,
            "MAIN_MESSAGE" => $message,
            "LOGIN_EMAIL" => $email,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_remind ()
    {
        $email = $this->GetValidGP ("email", "Email", VALIDATE_EMAIL);
        if ($this->errors['err_count'] > 0)
        {
            $this->Redirect ($this->pageUrl."?ec=wr");
        }
        $count = $this->db->GetOne ("Select Count(*) From `members` Where email='$email'", 0);
        if ($count > 0)
        {
            $siteTitle = $this->db->GetOne ("Select Value From settings Where Keyname='SiteTitle'");
            $contactEmail = $this->db->GetOne ("Select Value From settings Where Keyname='ContactEmail'");
            $username = $this->db->GetOne ("Select username From `members` Where email='$email'");
            $first_name = $this->db->GetOne ("Select first_name From `members` Where email='$email'");
            $last_name = $this->db->GetOne ("Select last_name From `members` Where email='$email'");
            $member_id = $this->db->GetOne ("Select member_id From `members` Where email='$email'");

            $password = getUnID (8);
            $password_code = md5 ($password);

            $this->db->ExecuteSql ("Update `members` Set passwd='$password_code' Where email='$email'");

            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='5'", ""); 
            if ($row ["is_active"] == 1)
            {
                
                $SiteTitle = $this->db->GetSetting ("SiteTitle");
                $SiteUrl = $this->db->GetSetting ("SiteUrl");
            
                $subject = $this->dec ($row ["subject"]);
                $message = $this->dec ($row ["message"]);
                $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                $RefLink = $SiteUrl."index.php?spon=".$member_id;
                
                $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                $message = preg_replace ("/\[Username\]/", $username, $message);
                $message = preg_replace ("/\[Email\]/", $email, $message);
                $message = preg_replace ("/\[Password\]/", $password, $message);
                $message = preg_replace ("/\[RefLink\]/", $RefLink, $message);
                
                sendMail ($email, $subject, $message, $this->emailHeader);
                
                $this->Redirect ($this->pageUrl."?ec=done");
                
            }
            else
            {
                $this->Redirect ($this->pageUrl."?ec=fail");    
            }
        }
        else 
        {
            $this->Redirect ($this->pageUrl."?ec=no");
        }
    }
}

//------------------------------------------------------------------------------
$zPage = new ZPage ("forgot");

$zPage->Render ();

?>
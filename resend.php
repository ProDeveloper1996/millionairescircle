<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);

        $this->mainTemplate = "./templates/resend.tpl";
        $this->pageTitle = "Re-send Verification Letter";
        $this->pageHeader = "Re-send Verification Letter";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $ec = $this->GetGp ('ec');
        $mess = "";
        if ($ec == 'yes') $mess = "<span class='message'>Tank you! The email has been successfully sent. Please check your email box</span>";
        if ($ec == 'no') $mess = "<span class='error'>Sorry. Cannot find not verified user with these username and password</span>";
        if ($ec == 'res') $mess = "<span class='error'>Sorry. This operation is forbidden by admin</span>";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MESSAGE" => $mess,
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $username = $this->enc($this->GetValidGP ("username", "Username", VALIDATE_USERNAME));
        $password = $this->GetValidGP ("password", "Password", VALIDATE_PASSWORD);
        $password_code = md5 ($password);
        if ($this->errors['err_count'] > 0)
        {
            $this->Redirect ($this->pageUrl."?ec=no");
        }
        $member_id = $this->db->GetOne("Select member_id From `members` Where username='$username' And passwd='$password_code' And is_active=0 And is_dead=0", "0");

        if ($member_id > 0)
        {
            $SiteTitle = $this->db->GetSetting ("SiteTitle");
            $SiteUrl = $this->db->GetSetting ("SiteUrl");
            
            $first_name = $this->dec ($this->db->GetOne("Select `first_name` From `members` Where `member_id`='$member_id'"));
            $last_name = $this->dec ($this->db->GetOne("Select `last_name` From `members` Where `member_id`='$member_id'"));
            $email = $this->db->GetOne("Select `email` From `members` Where `member_id`='$member_id'");           
            
            //member notification + activation link
            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='2'", ""); 
            if ($row ["is_active"] == 1)
            {
                $subject = $this->dec ($row ["subject"]);
                $message = $this->dec ($row ["message"]);
                $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                $alink = $SiteUrl."activation.php?code=".$member_id;
                
                $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                $message = preg_replace ("/\[Username\]/", $username, $message);
                $message = preg_replace ("/\[Email\]/", $email, $message);
                $message = preg_replace ("/\[Password\]/", $password, $message);
                $message = preg_replace ("/\[ActivationLink\]/", $alink, $message);
                
                sendMail ($email, $subject, $message, $this->emailHeader);
                
                $this->Redirect ("{$this->pageUrl}?ec=yes");
                
            }

            $this->Redirect ("{$this->pageUrl}?ec=res");
            

        }
        else
        {

            $this->Redirect ("{$this->pageUrl}?ec=no");

        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("resend");

$zPage->Render ();

?>
<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->mainTemplate = "./templates/security.tpl";
        $this->pageTitle = "Access Details Changing Page";
        $this->pageHeader = "Access Details Changing Page";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $mess = "";
        $ec = $this->GetGp("ec", "");
        $member_id = $this->member_id;
        $username =  $this->db->GetOne ("Select username From `members` Where member_id='$member_id'", "");
        if ($ec == "ok") $mess = "Thank you. Your access details have been successfully updated";

        $this->data = array (
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $mess,
            "USERNAME" => "<input type='text' name='username' value='".$username."' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
            "PASSWORD1" => "<input type='password' name='password1' value='' style='width:120px;'>",
            "PASSWORD2" => "<input type='password' name='password2' value='' style='width:120px;'>",
        );



    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {

        $member_id = $this->member_id;

        $username = $this->GetValidGP ("username", "Username", VALIDATE_USERNAME);
        $password1 = $this->GetValidGP ("password1", "Password", VALIDATE_PASSWORD);
        $password2 = $this->GetValidGP ("password2", $password1, VALIDATE_PASS_CONFIRM);

        $password = md5 ($this->enc ($this->GetValidGP ("password", "Password", VALIDATE_NOT_EMPTY)));

        $password_db = $this->db->GetOne ("Select passwd From `members` Where member_id='$member_id'", "");
        if ($password != $password_db) $this->SetError ("password", "Wrong current password");

        if ($this->errors['err_count'] > 0)
        {
             $this->data = array (
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_HEADER" => $this->pageHeader,
                "USERNAME" => "<input type='text' name='username' value='".$username."' style='width:120px;'>",
                "USERNAME_ERROR" => $this->GetError ("username"),
                "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
                "PASSWORD_ERROR" => $this->GetError ("password"),
                "PASSWORD1" => "<input type='password' name='password1' value='' style='width:120px;'>",
                "PASSWORD1_ERROR" => $this->GetError ("password1"),
                "PASSWORD2" => "<input type='password' name='password2' value='' style='width:120px;'>",
                "PASSWORD2_ERROR" => $this->GetError ("password2"),
            );
        }
        else
        {
            
            $siteTitle = $this->db->GetSetting ("SiteTitle");
            $email = $this->db->GetOne ("Select email From `members` Where member_id='$member_id'", "");
//            $username = $this->db->GetOne ("Select username From `members` Where member_id='$member_id'", "");
            $fname = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");

            $subject = "New access details From ".$siteTitle;

            $message = "Dear $fname,\r\n\r\n";
            $message .= "You have changed Access details to $siteTitle:\r\n";
            $message .= "Username : $username\r\n";
            $message .= "Password : $password1\r\n\r\n";
            $message .= $siteTitle;

            sendMail ($email, $subject, $message, $this->emailHeader);
            
            $password = md5 ($password1);
            $this->db->ExecuteSql ("Update `members` Set passwd='$password', username='$username' Where member_id='$member_id'");
            $this->Redirect ($this->pageUrl."?ec=ok");
        }
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("security");

$zPage->Render ();

?>
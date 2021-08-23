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

        $this->mainTemplate = "./templates/check_data_admin.tpl";
        $this->pageTitle = "Account activation";
        $this->pageHeader = "Account activation";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $pin_code = $this->GetGp ('c');
        $ec = $this->GetGp ('ec');
        $mess = "";
        if ($ec == 'yes') $mess = "<span class='message'>Your ip address was successfully changed</span>";
        if ($ec == 'no') $mess = "<span class='error'>Sorry but your ip address cannot be changed</span>";
        $ip_address = $this->GetServer ("REMOTE_ADDR", "unknown");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "IP_ADDRESS" => "<input type='text' name='ip_address' value='$ip_address' style='width:120px;'>",
            "PIN_CODE" => "<input type='text' name='c' value='$pin_code' style='width:120px;'>",
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
            "MESSAGE" => $mess,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $pin_code = $this->GetGp ('c');
        $ip_address = $this->GetGp ('ip_address');
        $username = $this->GetGp ('username');
        $password = md5 ($this->GetGp ('password'));

        $password_db = $this->db->GetSetting("AdminPassword");
        $username_db = $this->db->GetSetting("AdminUsername");

        $pin_code_in_db = $this->db->GetSetting("pin_code");
        if ($pin_code == $pin_code_in_db and $password_db == $password and $username == $username_db)
        {
            $this->db->SetSetting("IPAddress", $ip_address);
            $this->db->SetSetting("pin_code", "");
            $_SESSION['A_Login'] = $username;
            $_SESSION['A_Passwd'] = $password;
            $this->Redirect ("./admin/settings.php");
        }
        else
        {
            $this->Redirect ("{$this->pageUrl}?ec=no&c=$pin_code");
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "PIN_CODE" => "<input type='text' name='pin_code' value='$pin_code' style='width:120px;'>",
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("activate");

$zPage->Render ();

?>
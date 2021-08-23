<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
//require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->orderDefault = "Title";
        XPage::XPage ($object);

        $this->mainTemplate = "./templates/admindetails.tpl";
        $this->pageTitle = "Admin Settings";
        $this->pageHeader = "Admin Settings";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->pageTitle = "Admin Settings";
        $this->pageHeader = "Admin Settings";

        $ec = $this->GetGP ("ec");
        $message = ($ec == "yes")? "Changes were successfully saved." : "";
        //$adminUsername = $this->db->GetSetting ("AdminUsername");
        //$adminUsername = "<input type='text' name='AdminUsername' value='$adminUsername' maxlength='12' style='width:160px;'>";

        $adminPassword = "<input type='password' name='AdminPassword' value='' maxlength='14' style='width:160px;'>";
        $adminPassword1 = "<input type='password' name='AdminPassword1' value='' maxlength='14' style='width:160px;'>";
        $currentPassword = "<input type='password' name='CurrentPassword' value='' maxlength='14' style='width:160px;'>";

        $contactEmail = $this->db->GetSetting ("ContactEmail");
        $contactEmail = "<input type='text' name='ContactEmail' value='$contactEmail' style='width:300px;'>";

        $securityMode = $this->db->GetSetting ("SecurityMode");
        $securityMode = ($securityMode == 1)  ? "checked" : "";
        $securityMode = "<input type='checkbox' name='SecurityMode' value='1' $securityMode>";

        $useSMTPAutorisation = $this->db->GetSetting ("UseSMTPAutorisation");
        $useSMTPAutorisation = ($useSMTPAutorisation == 1)  ? "checked" : "";
        $useSMTPAutorisation = "<input type='checkbox' name='UseSMTPAutorisation' value='1' $useSMTPAutorisation>";

        $SMTPServer = $this->db->GetSetting ("SMTPServer");
        $SMTPServer = "<input type='text' name='SMTPServer' value='$SMTPServer' style='width:300px;'>";

        $SMTPDomain = $this->db->GetSetting ("SMTPDomain");
        $SMTPDomain = "<input type='text' name='SMTPDomain' value='$SMTPDomain' style='width:300px;'>";

        $SMTPUserName = $this->db->GetSetting ("SMTPUserName");
        $SMTPUserName = "<input type='text' name='SMTPUserName' value='$SMTPUserName' style='width:300px;'>";

        $SMTPPassword = $this->db->GetSetting ("SMTPPassword");
        $SMTPPassword = "<input type='text' name='SMTPPassword' value='$SMTPPassword' style='width:300px;'>";
        $adminAltPassword = "<input type='password' name='AdminAltPassword' value='' maxlength='14' style='width:160px;'>";
        $adminAltPassword1 = "<input type='password' name='AdminAltPassword1' value='' maxlength='14' style='width:160px;'>";
        $currentAdminAltPassword = "<input type='password' name='CurrentAdminAltPassword' value='' maxlength='14' style='width:160px;'>";

        $this->data = array (
            "ACTION_SCRIPT" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $message,
            //"MAIN_ADMIN_USERNAME" => $adminUsername,
            //"MAIN_ADMIN_USERNAME_ERROR" => $this->GetError ("AdminUsername"),
            "MAIN_ADMIN_PASSWORD" => $adminPassword,
            "MAIN_ADMIN_PASSWORD_ERROR" => $this->GetError ("AdminPassword"),
            "MAIN_ADMIN_PASSWORD1" => $adminPassword1,
            "MAIN_ADMIN_PASSWORD1_ERROR" => $this->GetError ("AdminPassword1"),
            "MAIN_CURRENT_PASSWORD" => $currentPassword,
            "MAIN_CURRENT_PASSWORD_ERROR" => $this->GetError ("CurrentPassword"),
            "MAIN_CONTACTEMAIL" => $contactEmail,
            "MAIN_USESMTPAUTORISATION" => $useSMTPAutorisation,
            "MAIN_SMTPSERVER" => $SMTPServer,
            "MAIN_SMTPDOMAIN" => $SMTPDomain,
            "MAIN_SMTPUSERNAME" => $SMTPUserName,
            "MAIN_SMTPPASSWORD" => $SMTPPassword,
            "MAIN_SECURITYMODE" => $securityMode,
            "MAIN_CURRENT_ALT_PASSWORD" => $currentAdminAltPassword,
            "MAIN_CURRENT_ALT_PASSWORD_ERROR" => $this->GetError ("CurrentAdminAltPassword"),
            "MAIN_ADMIN_ALT_PASSWORD" => $adminAltPassword,
            "MAIN_ADMIN_ALT_PASSWORD_ERROR" => $this->GetError ("AdminAltPassword"),
            "MAIN_ADMIN_ALT_PASSWORD1" => $adminAltPassword1,
            "MAIN_ADMIN_ALT_PASSWORD1_ERROR" => $this->GetError ("AdminAltPassword1"),
        );
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Settings";
        $this->pageHeader = "Settings";

        //$adminUsername = $this->GetValidGP ("AdminUsername", "Admin Username", VALIDATE_USERNAME);
        $contactEmail = $this->GetValidGP ("ContactEmail", "Contact Email", VALIDATE_EMAIL);

        $securityMode = $this->GetGP ("SecurityMode", "");
        $useSMTPAutorisation = $this->GetGP ("UseSMTPAutorisation", 0);
        $SMTPServer = $this->GetGP ("SMTPServer");
        $SMTPDomain = $this->GetGP ("SMTPDomain");
        $SMTPUserName = $this->GetGP ("SMTPUserName");
        $SMTPPassword = $this->GetGP ("SMTPPassword");

        $adminPassword = $this->GetGP ("AdminPassword");
        if ($adminPassword != "")
        {
            $adminPassword = $this->GetValidGP ("AdminPassword", "Admin Password", VALIDATE_PASSWORD);
            $adminPassword1 = $this->GetValidGP ("AdminPassword1", $adminPassword, VALIDATE_PASS_CONFIRM);
        }

        $currentPassword = md5 ($this->GetGP ("CurrentPassword"));
        $real_passwd = $this->db->GetSetting ("AdminPassword");
        if ($currentPassword != $real_passwd)
        {
            $this->SetError ("CurrentPassword", "Current Password is incorrect.");
        }


        $adminAltPassword = $this->GetGP ("AdminAltPassword");
        if ($adminAltPassword != "")
        {
            $adminAltPassword = $this->GetValidGP ("AdminAltPassword", "Admin Alternative Password", VALIDATE_PASSWORD);
            $adminAltPassword1 = $this->GetValidGP ("AdminAltPassword1", $adminAltPassword, VALIDATE_PASS_CONFIRM);

            $currentAltPassword = md5 ($this->GetGP ("CurrentAdminAltPassword"));
            $realAltpasswd = $this->db->GetSetting ("AdminAltPassword");
            if ($currentAltPassword != $realAltpasswd)
            {
                $this->SetError ("CurrentAdminAltPassword", "Current Alternative Admin Password is incorrect.");
            }
        }

        if ($this->errors['err_count'] > 0)
        {
            $securityMode = ($securityMode == 1)  ? "checked" : "";
            $useSMTPAutorisation = ($useSMTPAutorisation == 1)  ? "checked" : "";

            $this->data = array (
                "ACTION_SCRIPT" => $this->pageUrl,
                "MAIN_HEADER" => $this->pageHeader,
                //"MAIN_ADMIN_USERNAME" => "<input type='text' name='AdminUsername' value='$adminUsername' maxlength='12' style='width:160px;'>",
                //"MAIN_ADMIN_USERNAME_ERROR" => $this->GetError ("AdminUsername"),
                "MAIN_ADMIN_PASSWORD" => "<input type='password' name='AdminPassword' value='' maxlength='12' style='width:160px;'>",
                "MAIN_ADMIN_PASSWORD_ERROR" => $this->GetError ("AdminPassword"),
                "MAIN_ADMIN_PASSWORD1" => "<input type='password' name='AdminPassword1' value='' maxlength='12' style='width:160px;'>",
                "MAIN_ADMIN_PASSWORD1_ERROR" => $this->GetError ("AdminPassword1"),
                "MAIN_CURRENT_PASSWORD" => "<input type='password' name='CurrentPassword' value='' maxlength='12' style='width:160px;'>",
                "MAIN_CURRENT_PASSWORD_ERROR" => $this->GetError ("CurrentPassword"),
                "MAIN_ADMIN_ALT_PASSWORD" => "<input type='password' name='AdminAltPassword' value='' maxlength='12' style='width:160px;'>",
                "MAIN_ADMIN_ALT_PASSWORD_ERROR" => $this->GetError ("AdminAltPassword"),
                "MAIN_ADMIN_ALT_PASSWORD1" => "<input type='password' name='AdminAltPassword1' value='' maxlength='12' style='width:160px;'>",
                "MAIN_ADMIN_ALT_PASSWORD1_ERROR" => $this->GetError ("AdminAltPassword1"),
                "MAIN_CURRENT_ALT_PASSWORD" => "<input type='password' name='CurrentAdminAltPassword' value='' maxlength='14' style='width:160px;'>",
                "MAIN_CURRENT_ALT_PASSWORD_ERROR" => $this->GetError ("CurrentAdminAltPassword"),
                "MAIN_CONTACTEMAIL" => "<input type='text' name='ContactEmail' value='$contactEmail' style='width:300px;'>",
                "MAIN_CONTACTEMAIL_ERROR" => $this->GetError ("ContactEmail"),
                "MAIN_SECURITYMODE" => "<input type='checkbox' name='SecurityMode' value='1' $securityMode>",
                "MAIN_USESMTPAUTORISATION" => "<input type='checkbox' name='UseSMTPAutorisation' value='1' $useSMTPAutorisation>",
                "MAIN_SMTPSERVER" => "<input type='text' name='SMTPServer' value='$SMTPServer' style='width:300px;'>",
                "MAIN_SMTPDOMAIN" => "<input type='text' name='SMTPDomain' value='$SMTPDomain' style='width:300px;'>",
                "MAIN_SMTPUSERNAME" => "<input type='text' name='SMTPUserName' value='$SMTPUserName' style='width:300px;'>",
                "MAIN_SMTPPASSWORD" => "<input type='text' name='SMTPPassword' value='$SMTPPassword' style='width:300px;'>",
            );
        }
        else
        {
            //$this->db->SetSetting ("AdminUsername", $adminUsername);
            if ($adminPassword != "")
            {
                $adminPassword = md5 ($adminPassword);
                $this->db->SetSetting ("AdminPassword", $adminPassword);
            }

            if ($adminAltPassword != "")
            {
                $adminAltPassword = md5 ($adminAltPassword);
                $this->db->SetSetting ("AdminAltPassword", $adminAltPassword);
            }

            $ip_address = "";
            if ($securityMode == 1) $ip_address = $this->GetServer ("REMOTE_ADDR", "unknown");

            $this->db->SetSetting ("ContactEmail", $contactEmail);
            $this->db->SetSetting ("SecurityMode", $securityMode);
            $this->db->SetSetting ("UseSMTPAutorisation", $useSMTPAutorisation);
            $this->db->SetSetting ("SMTPServer", $SMTPServer);
            $this->db->SetSetting ("SMTPDomain", $SMTPDomain);
            $this->db->SetSetting ("SMTPUserName", $SMTPUserName);
            $this->db->SetSetting ("SMTPPassword", $SMTPPassword);
            $this->db->SetSetting ("IPAddress", $ip_address);
            $this->UpdateRegisterDetails ();
            $this->Redirect ($this->pageUrl."?ec=yes");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_downlog ()
    {
        $siteTitle = $this->db->GetOne ("Select value From settings Where keyname='SiteTitle'");

        $browser = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match ("/MSIE 5.5/", $browser) || preg_match ("/MSIE 6.0/", $browser))
        {
            header ('Content-Type: application/octetstream');
            header ('Content-Disposition: attachment; filename="admin_logs.log"');
            header ("Content-Transfer-Encoding: binary");
            header ('Expires: 0');
            header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header ('Pragma: public');
        }
        else
        {
            header ('Content-Type: application/octet-stream');
            header ('Content-Disposition: attachment; filename="admin_logs.log"');
            header ("Content-Transfer-Encoding: binary");
            header ('Expires: 0');
            header ('Pragma: no-cache');
        }

        $output = "";
        $result = $this->db->ExecuteSql ("Select * From `logs` Order By z_date Desc");
        while ($row = $this->db->FetchInArray ($result)) {
            $output .= date ("d-M-Y h:m:s", $row['z_date'])."\t".$row['ip_addr']."\t".$row['descr']."\r\n";
        }

        print ($output);

        exit ();
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("settings");

$zPage->Render ();

?>
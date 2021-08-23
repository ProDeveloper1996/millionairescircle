<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object = "none")
    {
		XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {

		$this->mainTemplate = "./templates/login.tpl";
        $this->pageTitle = "Administrative Panel";
        $this->pageHeader = "Administrative Panel";

        $login = "<input type='text' name='Login' placeholder='Username' value='".$this->GetGP ("login")."' maxlength='30' style='width:100%;'>";
        $passwd  = "<input type='password' name='Password' placeholder='Password' value='' maxlength='50' style='width:100%;'>";

        $this->data = array (
            "HEADER_TITLE" => $this->pageTitle,
            "MAIN_ACTION" => $this->pageUrl,
            "PAGE_TITLE" => $this->pageHeader,
            "LOGIN_ERROR" => $this->GetError ("login"),
            "LOGIN_USERNAME" => $login,
            "LOGIN_PASSWORD" => $passwd,
        );
    }


    //--------------------------------------------------------------------------
    function ocd_login ()
    {
        $result = $this->RegisterUser ();

        switch ($result)
        {
            case 1:
            		$ip_address = $this->GetServer ("REMOTE_ADDR", "unknown");
					if ($ip_address != "127.0.0.1") $this->GetServerVersion ();
                	$this->Redirect ("./members.php");
            break;
            case -2:
                $this->SetError ("login", "Username or Password is wrong. Please try again.");
            break;
            case -1:
                $this->SetError ("login", "IP-protection: you're trying to login from different IP address.<br>Please check your pin email.");
            break;
            default:
                $this->SetError ("login", "Some mistake. Please try later.");
        }
        $this->ocd_list ();
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ();

$zPage->Render ();

?>
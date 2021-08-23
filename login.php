<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");
//if (!defined('_ACCESS_') || _ACCESS_ != 'access_'._VERSION_ ) die('Access denied.');

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object = "none")
    {
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ($a = "")
    {
        GLOBAL $dict;
        
		  $this->mainTemplate = "./templates/login.tpl";
        $this->pageTitle = $dict['LOGIN_pageTitle'];
		  $this->pageHeader = $dict['LOGIN_pageTitle'];
        $siteUrl = $this->db->GetSetting ("SiteUrl");

        $username = "<input type='text' name='Username' value='".$this->GetGP ("Username")."' maxlength='16' style='width: 120px;'>";
        $passwd  = "<input type='password' name='Password' value='' maxlength='16' style='width: 120px;'>";
        $turing_id = $this->GetSession ("Log_Turing_ID", 0);
        $this->data = array (
            "MAIN_HEADER" => $this->pageTitle,
            "HEADER_JAVASCRIPTS" => $this->javaScripts,
            "PAGE_TITLE" => $this->pageHeader,
            "LOGIN_ERROR" => $this->GetError ("login"),
            "LOGIN_USERNAME" => $username,
            "LOGIN_PASSWORD" => $passwd,
            //"LOGIN_TURING" => "<input type='text' name='turing' value='' maxlength='5' style='width: 120px;' autocomplete='off'>",
            //"LOGIN_TURING_IMAGE" => "<img src='./includes/turing.php?PHPSESSID=".session_id()."' border='0'  class='img_w_board' align='absmiddle'>",
            "LOGIN_TURING_IMAGE" => "<img src='/captcha.php' border='0'  class='img_w_board' align='absmiddle'>",
        );
        
        $number_turing = $this->db->GetSetting ("number_turing", "0");
        if ($number_turing > 0)
        {
        		$this->data ["TURING"] = array ("_" => "_");
        }
    }


    //--------------------------------------------------------------------------
    function ocd_login ()
    {
        GLOBAL $dict;

        $result = $this->RegisterUser ();

        switch ($result)
        {
            case -3:
                $this->SetError ("login", $dict['LOGIN_Error1'] );
            break;
            case 1:
                $this->Redirect ("./member/myaccount.php");
            break;
            case 2:
                $this->SetError ("login", $dict['LOGIN_Error2']);
            break;
            case 3:
                $this->SetError ("login", $dict['LOGIN_Error3']);
            break;
            case 4:
                $this->SetError ("login", $dict['LOGIN_Error4']);
            break;
            case 5:
                $login = $this->GetGP ("Username", "");
                $this->SetError ("login", $dict['LOGIN_Error5']);
            break;
            default:
                $this->SetError ("login", $dict['LOGIN_Error6']);
        }
        $this->ocd_list ();
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("login");

$zPage->Render ();

?>
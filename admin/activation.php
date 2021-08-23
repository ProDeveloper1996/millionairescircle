<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->orderDefault = "Title";
        XPage::XPage ($object, false);

        $this->mainTemplate = "./templates/activation.tpl";
        $this->pageTitle = "Activation page";
        $this->pageHeader = "Activation page";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->pageTitle = "Activation Screen";
        $this->pageHeader = "Activation Screen";

        $secureCode1 = $this->GetGP ("SecureCode1");
        $secureCode2 = $this->GetGP ("SecureCode2");
        $secureCode3 = $this->GetGP ("SecureCode3");
        $secureCode4 = $this->GetGP ("SecureCode4");
        $secureCode = "<input type='text' name='SecureCode1' value='$secureCode1' maxlength='4' style='width: 46px; text-transform: uppercase;'>";
        $secureCode .= " - <input type='text' name='SecureCode2' value='$secureCode2' maxlength='4' style='width: 46px; text-transform: uppercase;'>";
        $secureCode .= " - <input type='text' name='SecureCode3' value='$secureCode3' maxlength='4' style='width: 46px; text-transform: uppercase;'>";
        $secureCode .= " - <input type='text' name='SecureCode4' value='$secureCode4' maxlength='4' style='width: 46px; text-transform: uppercase;'>";

        $this->data = array (
            "ACTION_SCRIPT" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_SECURECODE" => $secureCode,
            "MAIN_SECURECODE_ERROR" => $this->GetError ("SecureCode"),
        );
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $secureCode1 = $this->GetValidGP ("SecureCode1", "Secure Code", VALIDATE_NOT_EMPTY);
        $secureCode2 = $this->GetValidGP ("SecureCode2", "Secure Code", VALIDATE_NOT_EMPTY);
        $secureCode3 = $this->GetValidGP ("SecureCode3", "Secure Code", VALIDATE_NOT_EMPTY);
        $secureCode4 = $this->GetValidGP ("SecureCode4", "Secure Code", VALIDATE_NOT_EMPTY);

        $secureCode = $secureCode1 . "-" . $secureCode2 . "-" . $secureCode3 . "-" . $secureCode4;
        if ($this->errors['err_count'] == 0)
        {
            $domain_name = $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
            $domain_name = pathinfo ($domain_name);
            $domain_name = $domain_name["dirname"];
            $domain_name = substr ($domain_name, 0, -6);

            $querry = "t=0&";
            $querry .= "dname=$domain_name&";
            $querry .= "code=$secureCode&";
            $querry .= "ocd=check37";

            $querry_ru = $querry_com = $querry;

            $reply_com = "";
            $ch_com = curl_init ();
            curl_setopt ($ch_com, CURLOPT_URL, ASERVER_COM);
            curl_setopt ($ch_com, CURLOPT_POST, true);
            curl_setopt ($ch_com, CURLOPT_POSTFIELDS, $querry_com);
            curl_setopt ($ch_com, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch_com, CURLOPT_TIMEOUT, 60);
            curl_setopt ($ch_com, CURLOPT_HEADER, false);
            $reply_com = curl_exec ($ch_com);
            curl_close ($ch_com);

            $reply_ru = "";
/*        
            $ch_ru = curl_init ();
            curl_setopt ($ch_ru, CURLOPT_URL, ASERVER_RU);
            curl_setopt ($ch_ru, CURLOPT_POST, true);
            curl_setopt ($ch_ru, CURLOPT_POSTFIELDS, $querry_ru);
            curl_setopt ($ch_ru, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch_ru, CURLOPT_TIMEOUT, 60);
            curl_setopt ($ch_ru, CURLOPT_HEADER, false);
            $reply_ru = curl_exec ($ch_ru);
            curl_close ($ch_ru);
*/
//debug($reply_com);
            $reply_com = unserialize($reply_com)  ;
            if ( !is_array($reply_com) ) exit($reply_com);
            $license_key = $reply_com['license_key'];
            $reply_com = $reply_com['status'];
//debug($reply_com);
            //$t = explode(",", base64_decode(substr($license_key,2)) );
            if ( empty($license_key)  ) $reply_com='Access denied';
//debug($t);            
            if ($reply_com == "Activate" ) // Or $reply_ru == "Activate"
            {
                $this->SetCurrentVersion (1);
                $this->db->ExecuteSql ("Update settings Set value='$secureCode' Where keyname='SerialCode'");

                // Set possible settings
                $domain_name = getAbsoluteLink ($domain_name) . "/";
                $path = substr ($_SERVER["SCRIPT_FILENAME"], 0, -20);
                $this->db->ExecuteSql ("Update settings Set value='$domain_name' Where keyname='SiteUrl'");
                $this->db->ExecuteSql ("Update settings Set value='$path' Where keyname='PathSite'");
                $this->db->ExecuteSql ("Update settings Set value='".time ()."' Where keyname='StartDate'");

                $this->db->ExecuteSql ("Update settings Set value='".$license_key."' Where keyname='LicenseNumber'");

                $this->Redirect ("index.php");
            }
            else if ($reply_com == "" ) { //And $reply_ru == ""
                $this->SetError ("SecureCode", "The activation server is unaccessible. Please try later.");
            }
            else {
                $this->SetError ("SecureCode", "Entered Serial number is incorrect. Please try again. <br>Possibly your copy was blocked. Please contact us: support@runmlm.com");
            }
        }
        else $this->SetError ("SecureCode", "You should specify 'Serial number'");

        $this->ocd_list ();
    }

    //--------------------------------------------------------------------------
    function ocd_deactivate ()
    {
        $serialCode = $this->db->GetOne ("Select value From settings Where keyname='SerialCode'");
        $domain_name = $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
        $domain_name = pathinfo ($domain_name);
        $domain_name = $domain_name["dirname"];
        $domain_name = substr ($domain_name, 0, -6);

        $querry = "t=2&";
        $querry .= "dname=$domain_name&";
        $querry .= "code=$serialCode";

        $querry_ru = $querry_com = $querry;

        $reply_com = "";
        $ch_com = curl_init ();
        curl_setopt ($ch_com, CURLOPT_URL, ASERVER_COM);
        curl_setopt ($ch_com, CURLOPT_POST, true);
        curl_setopt ($ch_com, CURLOPT_POSTFIELDS, $querry_com);
        curl_setopt ($ch_com, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch_com, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch_com, CURLOPT_HEADER, false);
        $reply_com = curl_exec ($ch_com);
        curl_close ($ch_com);
        
        $reply_ru = "";
        $ch_ru = curl_init ();
        curl_setopt ($ch_ru, CURLOPT_URL, ASERVER_RU);
        curl_setopt ($ch_ru, CURLOPT_POST, true);
        curl_setopt ($ch_ru, CURLOPT_POSTFIELDS, $querry_ru);
        curl_setopt ($ch_ru, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch_ru, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch_ru, CURLOPT_HEADER, false);
        $reply_ru = curl_exec ($ch_ru);
        curl_close ($ch_ru);

        if ($reply_com == "Complete" Or $reply_ru == "Complete")
        {
            $this->SetCurrentVersion (0);
            $this->db->ExecuteSql ("Update settings Set value='' Where keyname='SerialCode'");
        }

        $this->Redirect ($this->pageUrl);
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("activation");

$zPage->Render ();

?>
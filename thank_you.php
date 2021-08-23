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
        GLOBAL $dict;
		  XPage::XPage ($object);
        $this->mainTemplate = "./templates/thank_you.tpl";
        $this->pageTitle = $dict['TU_pageTitle'];
        $this->pageHeader = $dict['TU_pageTitle'];
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $contactEmail = $this->db->GetSetting ("ContactEmail");
        $email = $this->GetGP ("email", "");

        if ($email == "") $this->Redirect ("index.php");

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "CONTACT_EMAIL" => $contactEmail,
            "EMAIL" => $email,

        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("Thankyou");

$zPage->Render ();

?>
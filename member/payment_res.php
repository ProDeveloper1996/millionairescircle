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
        GLOBAL $dict;
		  $this->mainTemplate = "./templates/payment_res.tpl";
        $this->pageTitle = $dict['PM_pageTitle2'];
        $this->pageHeader = $dict['PM_pageTitle2'];
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
        $message = "";
        $res = $this->GetGP ("res", "");

        if ($res == "ok") $message = $dict['PM_mess5'];
        if ($res == "no") $message = $dict['PM_mess6'];
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "PAYMENT_RESULT" => $message,
       );
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("result");

$zPage->Render ();

?>
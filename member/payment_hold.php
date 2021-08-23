<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_member.php");
require_once("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage($object)
    {
        XPage::XPage($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list()
    {
        GLOBAL $dict;
        $this->pageTitle = $dict['PM_pageTitle'];
        $this->pageHeader = $dict['PM_pageTitle'];
        $this->mainTemplate = "./templates/payment_hold.tpl";
        $member_id = $this->member_id;

        $mess = "";
        if ($this->GetGP("res", "") == "no") $mess = $dict['PM_mess1'];
        if ($this->GetGP("res", "") == "noa") $mess = $dict['PM_mess1'];

        $cycling = $this->db->GetSetting("cycling", 1);
        if ($cycling == 1) $this->Redirect("payment.php");

        $CHECKED = ($this->db->GetOne ("Select prelaunch_norif From `members` Where member_id='$member_id'", 0)?'checked':'');

        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            'CHECKED' => $CHECKED
        );

    }

    //--------------------------------------------------------------------------
    function ocd_inform()
    {
        $inform = ($this->GetGP("inform",'')=='on'?1:0);
        $member_id = $this->member_id;
        $this->db->ExecuteSql ("Update `members` Set prelaunch_norif='$inform' Where member_id='$member_id'");
        $this->Redirect($this->pageUrl);
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("");

$zPage->Render();

?>
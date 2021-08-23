<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_admin.php");
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
        $st = $this->GetGP("st", "");
        $message = "";
        if ($st == "ok") $message = "Changes were successfully saved";
        if ($st == "no") $message = "Please fill in all the fields";


        $this->mainTemplate = "./templates/pre_launch.tpl";
        $this->pageTitle = "Pre Launch Settings";
        $this->pageHeader = "Pre Launch Settings";

        $date = $this->db->GetSetting("PRE_LAUNCH_DATE");
        if ($date==0) $date = time();

        $PRE_LAUNCH_DATE = getDaySelect(date("d", $date), "dateDay") . getMonthSelect(date("m", $date), "dateMonth") . getYearSelect(date("Y", $date), "dateYear");

        $PRE_LAUNCH = $this->db->GetSetting("PRE_LAUNCH");
        $PRE_LAUNCH = ($PRE_LAUNCH == 1) ? "<input type='checkbox' name='PRE_LAUNCH' value='1' checked>" : "<input type='checkbox' name='PRE_LAUNCH' value='1'>";
        $PRE_LAUNCH .= "&nbsp;<span class='signs_b'>On/Off</span>";

        $TIME_AFTER_LAUNCH = $this->db->GetSetting("time_after_launch");
        
        $this->data = array(
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $message,
            "PRE_LAUNCH" => $PRE_LAUNCH,
            "PRE_LAUNCH_DATE" => $PRE_LAUNCH_DATE,
            'TIME_AFTER_LAUNCH' => $TIME_AFTER_LAUNCH

        );

    }

    //--------------------------------------------------------------------------
    function ocd_update()
    {
        $PRE_LAUNCH = $this->GetGP("PRE_LAUNCH", 0);
        $date = mktime(0, 0, 0, $this->GetGP("dateMonth"), $this->GetGP("dateDay"), $this->GetGP("dateYear"));

        if ($PRE_LAUNCH==0)  $date = 0;

        $this->db->SetSetting("PRE_LAUNCH", $PRE_LAUNCH);
        $this->db->SetSetting("PRE_LAUNCH_DATE", $date);

        $time_after_launch = (int)$this->GetGP ("time_after_launch", '36');
        $this->db->SetSetting ("time_after_launch", $time_after_launch);
        
        $this->Redirect($this->pageUrl . "?st=ok");


    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("");

$zPage->Render();

?>


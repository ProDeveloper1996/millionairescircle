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
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $member_id = $this->GetGP("member_id", 0);
        $level = $this->GetGP("level", 0);
        $initial_date = mktime (0, 0, 0, date ('m'), 1, date ('Y'));
        $array_members = getMembers ($member_id, $level);
        
        $content = "<div id='resultik$level'><table bgcolor='#76889d' cellpadding='4' cellspacing='0' class='b_border' width='100%'>";
        $content .= "<tr bgcolor='#65768a'><td class='b_border'><b class='pages'>ID</b></td><td class='b_border'><b class='pages'>A</b></td><td class='b_border'><b class='pages'>Name</b></td><td class='b_border'><b class='pages'>Member Level</b></td><td class='b_border'><b class='pages'>Enrolled By</b></td></tr>";
        
        foreach ($array_members as $each)
        {
            
            $member = $this->db->GetEntry ("Select * From `members` Where member_id='$each'");
            $m_level = $member ["m_level"];
            $enroller_id = $member ["enroller_id"];
            $name = $this->dec ($member ["first_name"] . " " . $member ["last_name"]);
            $style = "";
            $title = $this->db->GetOne ("Select `title` From `types` Where `order_index`='$m_level'", "");
            $style .= ($enroller_id == $member_id)? "font-weight:bold;" : "font-weight:normal;";
            $content .= "<tr><td class='b_border' align='left' width='120'> ID# $each </td><td class='b_border' width='16'><a href='contact.php?s=$each'><img src='./images/mail.png' border='0' alt='Email Member' title='Email Member'></a></td><td class='b_border' align='left'><span style='$style'>$name</style></td><td class='b_border' width='30%'>$title</td><td class='b_border' width='120'>ID# $enroller_id</td></tr>";
        }
        
        $content .= "<tr bgcolor='#65768a'><td class='b_border' align='center' colspan='5'><a href='javascript:close_level($member_id, $level)'><img src='./images/close_up.gif' border='0' align='bottom' alt='Hide Details' title='Hide Details' /></td></tr>";
        $content .= "</table></div>";
        
        print ($content);
        exit ();
        
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month");

$zPage->Render ();

?>
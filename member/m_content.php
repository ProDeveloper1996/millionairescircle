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
        
        $member_id = $this->member_id;
        $m_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$member_id'", 0);
        
        $p_id = $this->GetID ("p_id");
        if ($p_id > 1)
        {
            $count = $this->db->GetOne ("Select Count(*) From `pages` Where page_id='$p_id' And is_active=1 And is_member=1 And level".$m_level."=1", 0);
            if ($count == 0) $this->Redirect ("overview.php");
            $title = $this->dec ($this->db->GetOne ("Select title From `pages` Where page_id='$p_id'", ""));
            $content = $this->dec ($this->db->GetOne ("Select content From `pages` Where page_id='$p_id'", ""));
        }
        else
        {
            $this->Redirect ("overview.php");
        }
        
        $this->mainTemplate = "./templates/m_content.tpl";
        $this->pageTitle = $title;
        $this->pageHeader = $title;
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
        );
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("Overview");

$zPage->Render ();

?>
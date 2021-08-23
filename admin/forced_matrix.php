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
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/forced_matrix.tpl";
        $id = $this->GetGP ("id");

        $first_name = $this->db->GetOne ("Select first_name From `members` Where member_id='$id'", "");
        $last_name = $this->db->GetOne ("Select last_name From `members` Where member_id='$id'", "");
        $m_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$id'", "");

        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> - Matrix View ($first_name $last_name)";
        $this->pageTitle = "Matrix View";        $content = "";
        $content = matrix_tree_admin_set ($id, $m_level);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
            "MAIN_ID" => $id,
            "MAIN_FIRST_NAME" => $first_name,
            "MAIN_LAST_NAME" => $last_name,
            "MAIN_LEVELS" => "<a href='m_levels.php?c_m_id=$id'><img title='See Levels' src='./images/levels_icon.png' valign='middle' border='0'></a>",
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("forced_matrix");

$zPage->Render ();

?>
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
        $matrix_type = $this->db->GetSetting ("cycling", 0);
        if ($matrix_type == 1)
        {
            $this->mainTemplate = "./templates/memb_matrix_cycl.tpl";
            $id = $this->GetGP ("id");
            $level = $this->GetGP ("level", 1);
            $first_name = $this->db->GetOne ("Select first_name From members Where member_id='$id'");
            $last_name = $this->db->GetOne ("Select last_name From members Where member_id='$id'");
            $this->pageHeader = "<a href='members.php'>Members</a> / Matrix View ($first_name $last_name)";
            $this->pageTitle = "Matrix View";
            $content = "";
            $links = "";
            $result = $this->db->ExecuteSql ("Select * From `types` Order By order_index ASC");
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row['order_index'];
                $title = $this->dec ($row['title']);
                $class = ($order_index == $level)? "menu_invert" : "menu";
                $links .= "&nbsp;&nbsp;<a href='{$this->pageUrl}?id=$id&level=$order_index'>$title</a>";
            }

            $content = matrix_tree_admin_set ($id, $level);

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CONTENT" => $content,
                "MAIN_ID" => $id,
                "MAIN_LINKS" => $links,
                "MAIN_FIRST_NAME" => $first_name,
                "MAIN_LAST_NAME" => $last_name,
            );
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("memb_matrix");

$zPage->Render ();

?>
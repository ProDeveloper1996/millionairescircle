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
        $level = $this->GetGP ("level", 0);
        $id = $this->GetGP ("id", 0);
        $matrix_type = $this->db->GetSetting ("cycling", 0);
        if ($matrix_type == 1)
        {
            $this->mainTemplate = "./templates/memb_matrix_cycl.tpl";
            $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> - All Matrix View";
            $this->pageTitle = "Matrix View";
            $content = "";
            $links = "";
            $result = $this->db->ExecuteSql ("Select * From `types` Order By order_index ASC");
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row['order_index'];
                $title = $this->dec ($row['title']);
                $class = ($order_index == $level)? "menu_invert" : "menu";
                $links .= "&nbsp;&nbsp;<a class='$class' href='{$this->pageUrl}?id=$id&level=$order_index'>$title</a>";
            }

            $content = matrix_tree_admin_set (1, $level);

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CONTENT" => $content,
                "MAIN_ID" => $id,
                "MAIN_LINKS" => $links,
                //"MAIN_FIRST_NAME" => $first_name,
                //"MAIN_LAST_NAME" => $last_name,
            );
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("memb_matrix");

$zPage->Render ();

?>
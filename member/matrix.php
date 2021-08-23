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
		 GLOBAL $dict;
        XPage::XPage ($object);
   }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
		 GLOBAL $dict;
        $id = $this->member_id;

        $content = "";
        $first_name = $this->db->GetOne ("Select first_name From members Where member_id='$id'");
        $last_name = $this->db->GetOne ("Select last_name From members Where member_id='$id'");
        $cycling = $this->db->GetSetting ("cycling", 0);

        if ($cycling == 1)
        {
            $this->mainTemplate = "./templates/matrix.tpl";
            $level = $this->GetGP ("level", 1);
            $this->pageHeader = $dict['MX_pageTitle'] ;//. ' / ' . '<a href="./tree_matrix.php">'.$dict['MX_Tree'].'</a>' ;
            if (isset($this->LicenseAccess['tree_matrix']) && $this->LicenseAccess['tree_matrix'][$this->lic_key]==1)
                $this->pageHeader = $this->pageHeader . ' / ' . '<a href="./tree_matrix.php">'.$dict['MX_Tree'].'</a>' ;
            $this->pageTitle = $dict['MX_pageTitle'];
            $content = "";
            $links = "";
            $result = $this->db->ExecuteSql ("Select * From `types` Order By order_index ASC");
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row['order_index'];
                $title = $this->dec ($row['title']);
                $class = ($order_index == $level)? "active" : "";
                //$links .= "&nbsp;&nbsp;&nbsp;&nbsp;<a class='$class' href='{$this->pageUrl}?id=$id&level=$order_index'>$title</a>";
                $links .= '<li role="presentation" class="'.$class.'"><a href="'.$this->pageUrl.'?id='.$id.'&level='.$order_index.'" >'.$title.'</a></li>';
            }

            $content = matrix_tree_member_set ($id, $level);
            
            if ($content == "") $content = "<span class='question'>{$dict['MX_NotEn']}";
            
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CONTENT" => $content,
                "MAIN_ID" => $id,
                "MAIN_LINKS" => $links,
                "MAIN_FIRST_NAME" => $first_name,
                "MAIN_LAST_NAME" => $last_name,
            );
        }
        else
        {
            $this->mainTemplate = "./templates/matrix_f.tpl";
            $this->pageHeader = $dict['MX_pageTitle'];
            $this->pageTitle = $dict['MX_pageTitle'];
            $content = "";
            $level = $this->db->GetOne ("Select m_level From `members` Where member_id='$id'", 0);
            $content = matrix_tree_member_set ($id, $level);
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CONTENT" => $content,
                "MAIN_FIRST_NAME" => $first_name,
                "MAIN_LAST_NAME" => $last_name,
                "MAIN_ID" => $id,
            );    
        }
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("memb_matrix");

$zPage->Render ();

?>
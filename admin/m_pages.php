<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");

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
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/m_pages.tpl";
        $this->pageTitle = "Member Area Pages";
        $this->pageHeader = "Member Area Pages";

        $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where is_member=1");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='To add a page'><img src='./images/add.png' border='0'></a>",
            "HEAD_ORDER" => "ID",
            "HEAD_NAME" => "Page Title",
            "HEAD_TITLE" => "Page name in menu",
            "HEAD_DESTINATION" => "Visible for (levels)",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_member=1 Order By order_index Asc", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['page_id'];
                $p_order = $row['order_index'];
                $title = $row['title'];
                $menu_title = $row['menu_title'];
                $destination = "";
                
                for ($i = 0; $i <= $count_levels - 1; $i++)
                {
                    $d = $i + 1;
                    
                    $destination .= ($row ["level$d"] == 1)? $this->db->GetOne ("Select CONCAT(order_index, '. ', title) From `types` Where order_index='$d'", "")." " : "";
                }                
                
                if ($total == 1)
                {
                    $orderLink = "&nbsp;";    
                }
                elseif ($p_order == $total)
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' alt='Move Up' title='Move Up' /></a>";
                }
                elseif ($p_order == 1)
                {
                     $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' alt='Move Down' title='Move Down' /></a>";
                }
                else
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' alt='Move Up' title='Move Up' /></a>";
                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' alt='Move Down' title='Move Down' /></a>";
                }
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"page_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' alt='Change activity status' title='Change activity status' /></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' alt='Edit' title='Edit' /></a>";
                $delLink = ($id > 2)? "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this page?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>" : "&nbsp;";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ORDER" => $p_order,
                    "ROW_ID" => $id,
                    "ROW_TITLE" => $title,
                    "ROW_MENU" => $menu_title,
                    "ROW_DESTINATION" => $destination,
                    "ROW_ORDERLINK" => ($id > 2)? $orderLink : "&nbsp;",
                    "ROW_ACTIVELINK" => ($id > 2)? $activeLink : "&nbsp;",
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor,
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/m_page_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript ();
        $arrLevels = array ();
        $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where page_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$row["menu_title"]."' maxlength='120' style='width: 300px;'>";
                //$content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["content"]."</textarea>";
                $content = $this->FCKeditor( "content", htmlspecialchars_decode($row["content"]) );

                for ($i = 0; $i <= $count_levels - 1; $i++)
                {
                    $d = $i + 1;
                    $arrLevels [] = $row ["level$d"];    
                }

            break;

            case FORM_FROM_GP:

                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$this->GetGP ("menu_title")."' maxlength='120' style='width: 300px;'>";
                //$content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$this->GetGP ("content")."</textarea>";
                $content = $this->FCKeditor( "content", htmlspecialchars_decode($this->GetGP ("content")) );
                
                for ($i = 0; $i <= $count_levels - 1; $i++)
                {
                    $arrLevels [] = $this->GetGP ("level$i", 0);    
                }
                
            break;

            case FORM_EMPTY:
            default:

                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                //$content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $content = $this->FCKeditor( "content", '' );
                $menu_title = "<input type='text' name='menu_title' value='' maxlength='120' style='width: 300px;'>";
                for ($i = 0; $i <= $count_levels - 1; $i++)
                {
                    $arrLevels [] = 1;    
                }

            break;
        }
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),

            "MAIN_TITLE_MENU" => $menu_title,
            "MAIN_TITLE_MENU_ERROR" => $this->GetError ("menu_title"),

            "MAIN_CONTENT" => $content,
            
            "MAIN_LEVELS" => $this->selectLevels ($arrLevels),            

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );


    }

    //--------------------------------------------------------------------------
    function selectLevels ($arrLevels)
    {
        $count_levels = count ($arrLevels);
        $toRet = "<table border='0' cellspacing='0' cellpadding='2'>";
        for ($i = 0; $i <= $count_levels - 1; $i++)
        {
            $d = $i + 1;
            $title = $this->db->GetOne ("Select title From `types` Where order_index='$d'", ""); 
            $checked = ($arrLevels [$i] == 1)? "checked" : "";
            $toRet .= "<tr><td>".$title . "</td><td><input type='checkbox' name='level$i' value='1' $checked> </td></tr>";
        }
        
        $toRet .= "</table>"; 
        return $toRet;
    }
    
    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Area Pages</a> / New Page";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Area Pages</a> / New Page";
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", "Name of the page in menu", VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            if ($this->errors['err_count'] > 0)
            {
                $this->fill_form ("insert", FORM_FROM_GP);
            }
            else
            {
                $fields = "";
                $values = "";
                $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
                for ($i = 0; $i <= $count_levels - 1; $i++)
                {
                    $d = $i + 1;
                    $res = $this->GetGP ("level$i", 0);
                    
                    $fields .= ", level".$d;
                    $values .= ", $res";
                        
                }
                
                $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where is_member=1", 0) + 1;
                $this->db->ExecuteSql ("Insert into {$this->object} (title, menu_title, content, order_index, is_member, is_active $fields) values ('$title', '$menu_title', '$content', '$total', 1, 0 $values)");
                $this->Redirect ($this->pageUrl);
            }
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Area Pages</a> / Edit Page";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Area Pages</a> / Edit Page";
        $id = $this->GetGP ("id");
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", "Name of the page in menu", VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $sql = "";
            $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
            
            for ($i = 0; $i <= $count_levels - 1; $i++)
            {
                $d = $i + 1;
                $res = $this->GetGP ("level$i", 0);
                $sql .= ", level".$d."=".$res;
            }
            
            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', menu_title='$menu_title', content='$content' $sql Where page_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where page_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $p_order = $this->db->GetOne ("Select order_index From `{$this->object}` Where page_id='$id'");
        $this->db->ExecuteSql ("Delete From {$this->object} Where page_id='$id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order' And is_member=1");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where page_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select page_id From {$this->object} Where order_index='$number_next' And is_member=1", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where page_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where page_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where page_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne ("Select page_id From {$this->object} Where order_index='$number_next' And is_member=1", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where page_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where page_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("pages");

$zPage->Render ();

?>
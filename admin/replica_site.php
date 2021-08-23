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
        
        $member_id = $this->GetGP ("id", 0);
        
        if ($member_id > 0) $this->SaveStateValue ("member_id", $member_id);
        $member_id = $this->GetStateValue ("member_id", 0);
        
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        $replica = $this->db->GetOne ("Select replica From `members` Where member_id='$member_id'", "");
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        
        $full_r_Url = $siteUrl.$replica."/";
        
        $r_siteUrl = ($replica != "")? "<a href='$full_r_Url' target='_blank'>$full_r_Url</a>" : "None (member did not create replicated site yet)";
        
        $this->mainTemplate = "./templates/r_pages.tpl";
        $this->pageTitle = "$name's Pages";
       
        $this->pageHeader = "<a href='members.php'>Members</a> / $name's pages";
        
        $quant_replica = $this->db->GetSetting ("quant_replica", 0);
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => ($total < $quant_replica)? "<a href='{$this->pageUrl}?ocd=new' title='Add a page'><img src='./images/add.png' border='0'></a>" : "<img src='./images/add.png' border='0' title='Sorry. You can not create more pages due to the limitation in site settings'>",
            "HEAD_ORDER" => "Order",
            "HEAD_NAME" => "Page Title",
            "HEAD_TITLE" => "Page name in menu",
            "R_SITE_URL" => $r_siteUrl,
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where member_id='$member_id' Order By order_index Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['replica_id'];
                $p_order = $row['order_index'];
                $title = $row['title'];
                $menu_title = $row['menu_title'];
                if ($total == 1)
                {
                    $orderLink = "&nbsp;";    
                }
                elseif ($p_order == $total)
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' title='Move Up'></a>";
                }
                elseif ($p_order == 1)
                {
                     $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' title='Move Down'></a>";
                }
                else
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' title='Move Up'></a>";
                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' title='Move Down'></a>";
                }
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"replica_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' title='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' title='Edit'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this page?');\"><img src='./images/trash.png' title='Delete site'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ORDER" => $p_order,
                    "ROW_ID" => $id,
                    "ROW_TITLE" => $title,
                    "ROW_MENU" => $menu_title,
                    "ROW_ORDERLINK" => $orderLink,
                    "ROW_ACTIVELINK" => $activeLink,
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
        $this->mainTemplate = "./templates/r_page_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where replica_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$row["menu_title"]."' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["content"]."</textarea>";

            break;

            case FORM_FROM_GP:

                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$this->GetGP ("menu_title")."' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$this->GetGP ("content")."</textarea>";

            break;

            case FORM_EMPTY:
            default:

                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $menu_title = "<input type='text' name='menu_title' value='' maxlength='120' style='width: 300px;'>";

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

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $member_id = $this->GetStateValue ("member_id", 0);
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / <a href='{$this->pageUrl}' class='ptitle'>$name's pages</a> / New Page";
        
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $member_id = $this->GetStateValue ("member_id", 0);
        $quant_replica = $this->db->GetSetting ("quant_replica", 0);
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
        if ($quant_replica == $total) $this->Redirect ($this->pageUrl);
         
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / <a href='{$this->pageUrl}' class='ptitle'>$name's pages</a> / New Page";

        
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
                $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where member_id='$member_id'", 0) + 1;
                $this->db->ExecuteSql ("Insert into {$this->object} (title, menu_title, content, order_index, member_id, is_active) values ('$title', '$menu_title', '$content', '$total', '$member_id', 1)");
//                print "Insert into {$this->object} (title, menu_title, content, order_index, member_id, is_active) values ('$title', '$menu_title', '$content', '$total', '$member_id', 0)";
                $this->Redirect ($this->pageUrl);
            }
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $member_id = $this->GetStateValue ("member_id", 0);
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / <a href='{$this->pageUrl}' class='ptitle'>$name's pages</a> / Edit Page";

        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $member_id = $this->GetStateValue ("member_id", 0);
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / <a href='{$this->pageUrl}' class='ptitle'>$name's pages</a> / Edit Page";

        $id = $this->GetGP ("id");
        $owner = $this->db->GetOne ("Select member_id From `{$this->object}` Where replica_id='$id'", "");
        if ($owner != $member_id) $this-Redirect ($this->pageUrl);
        
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", "Name of the page in menu", VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {

            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', menu_title='$menu_title', content='$content' Where replica_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where replica_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->GetStateValue ("member_id", 0);
        $p_order = $this->db->GetOne ("Select order_index From `{$this->object}` Where replica_id='$id'");
        $this->db->ExecuteSql ("Delete From {$this->object} Where replica_id='$id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order' And member_id='$member_id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->GetStateValue ("member_id", 0);
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where replica_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select replica_id From {$this->object} Where order_index='$number_next' And member_id='$member_id'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where replica_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where replica_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->GetStateValue ("member_id", 0);
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where replica_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne ("Select replica_id From {$this->object} Where order_index='$number_next' And member_id='$member_id'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where replica_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where replica_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script language='JavaScript' src='../admin/editor/scripts/innovaeditor.js'></script>
        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("replicas");

$zPage->Render ();

?>
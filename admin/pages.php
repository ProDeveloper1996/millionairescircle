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
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/pages.tpl";
        $this->pageTitle = "Public Area Pages";
        $this->pageHeader = "Public Area Pages";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where is_member=0");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => ($this->lic_key=='FREE' || $this->lic_key=='STARTER'?'':"<a href='{$this->pageUrl}?ocd=new' title='Add Public page'><img src='./images/add.png'></a>"),
            "HEAD_ORDER" => "ID",
            "HEAD_NAME" => "Page Title",
            "HEAD_TITLE" => "Page name in menu",
            "HEAD_URL" => "Page URL",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $siteUrl = $this->db->GetSetting ("SiteUrl");
            
            if ($this->lic_key=='FREE' || $this->lic_key=='STARTER') 
                $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_member=0 and page_id<=2 Order By order_index Asc", true);
            else 
                $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_member=0 Order By order_index Asc", true);

            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['page_id'];
                $p_order = $row['order_index'];
                $title = $row['title'];
                $menu_title = $row['menu_title'];
                
                $page_url = $siteUrl."content.php?p_id=".$id;
                
                if ($total == 3)
                {
                    $orderLink = "&nbsp;";    
                }
                elseif ($p_order == $total)
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' alt='Up' title='Up' /></a>";
                }
                elseif ($p_order == 3)
                {
                     $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' alt='Down' title='Down' /></a>";
                }
                else
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' alt='Up' title='Up' /></a>";
                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' alt='Down' title='Down' /></a>";
                }
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"page_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' alt='Change activity status' title='Change activity status' /></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' alt='Edit' title='Edit' /></a>";
                $delLink = ($id > 2)? "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this page?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>" : "&nbsp;";
                
                $menuLink = "<a href='javascript:is_menu(\"".$this->object."\", \"page_id\", ".$id.")'><img src='./images/mactive".$row['in_menu'].".png' width='25' border='0' alt='Show in menu status' title='Show in menu status' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ORDER" => $p_order,
                    "ROW_ID" => $id,
                    "ROW_TITLE" => $title,
                    "ROW_MENU" => $menu_title,
                    
                    "ROW_URL" => ($id > 1)? $page_url : $siteUrl,
                    
                    "ROW_ORDERLINK" => ($id > 2)? $orderLink : "&nbsp;",
                    "ROW_ACTIVELINK" => ($id > 2)? $activeLink : "&nbsp;",
                    "ROW_MENULINK" => ($id > 2)? $menuLink : "&nbsp;",
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
        $this->mainTemplate = "./templates/page_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where page_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$row["menu_title"]."' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["content"]."</textarea>";
                $keywords = "<input type='text' name='keywords' value='".$row["keywords"]."' maxlength='250' style='width: 600px;'>";
                $description = "<input type='text' name='description' value='".$row["description"]."' maxlength='250' style='width: 600px;'>";

                $content = $this->FCKeditor( "content", htmlspecialchars_decode($row["content"]) );

            break;

            case FORM_FROM_GP:

                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$this->GetGP ("menu_title")."' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$this->GetGP ("content")."</textarea>";
                $keywords = "<input type='text' name='keywords' value='".$this->GetGP ("keywords")."' maxlength='250' style='width: 600px;'>";
                $description = "<input type='text' name='description' value='".$this->GetGP ("description")."' maxlength='250' style='width: 600px;'>";

                $content = $this->FCKeditor( "content", htmlspecialchars_decode($this->GetGP ("content")) );

            break;

            case FORM_EMPTY:
            default:

                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                $content = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $menu_title = "<input type='text' name='menu_title' value='' maxlength='120' style='width: 300px;'>";
                $keywords = "<input type='text' name='keywords' value='' maxlength='250' style='width: 600px;'>";
                $description = "<input type='text' name='description' value='' maxlength='250' style='width: 600px;'>";

                $content = $this->FCKeditor( "content", '' );

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

            "MAIN_KEYWORDS" => $keywords,
            "MAIN_DESCRIPTION" => $description,

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        if ($this->lic_key=='FREE' || $this->lic_key=='STARTER')  exit('Access denied');
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Public Area Pages</a> / New Page";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        if ($this->lic_key=='FREE' || $this->lic_key=='STARTER')  exit('Access denied');
        $this->pageTitle = "New Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Public Area Pages</a> / New Page";
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", "Name of the page in menu", VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        $keywords = $this->enc ($this->GetGP ("keywords"));
        $description = $this->enc ($this->GetGP ("description"));

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
                $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where is_member=0", 0) + 1;
                $this->db->ExecuteSql ("Insert into {$this->object} (title, menu_title, content, keywords, description, order_index, is_member, is_active) values ('$title', '$menu_title', '$content', '$keywords', '$description', '$total', 0, 0)");
                $this->Redirect ($this->pageUrl);
            }
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Public Area Pages</a> / Edit Page";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Public Area Pages</a> / Edit Page";
        $id = $this->GetGP ("id");
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", "Name of the page in menu", VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        $keywords = $this->enc ($this->GetGP ("keywords"));
        $description = $this->enc ($this->GetGP ("description"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {

            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', menu_title='$menu_title', content='$content', keywords='$keywords', description='$description'  Where page_id='$id'");
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
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order' And is_member=0");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where page_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select page_id From {$this->object} Where order_index='$number_next' And is_member=0", 0);
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
        $id_next = $this->db->GetOne ("Select page_id From {$this->object} Where order_index='$number_next' And is_member=0", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where page_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where page_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

<!--        <script language='JavaScript' src='./editor/scripts/innovaeditor.js'></script>-->
        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("pages");

$zPage->Render ();

?>

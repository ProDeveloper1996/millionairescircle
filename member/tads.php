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
        GLOBAL $dict;
        $member_id = $this->member_id;
        $m_status = getStatus ($member_id);
        if ($m_status != "active") $this->Redirect ("./overview.php");
        
        $this->mainTemplate = "./templates/tads.tpl";
        $this->pageTitle = $dict['TD_pageTitle'];
        $this->pageHeader = $dict['TD_pageTitle'];
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
        $quant_textadds = $this->db->GetSetting ("quant_textadds");
        $quant_textadds_show = $this->db->GetSetting ("quant_textadds_show");
        $quant_textadds_show_m = $this->db->GetSetting ("quant_textadds_show_m");
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => ($total < $quant_textadds)? "<a href='{$this->pageUrl}?ocd=new' title='{$dict['TD_add']}'><img src='./images/add.png' border='0'></a>" : "&nbsp;",
            "NUMBER" => $quant_textadds,
            "SHOW_P" => $quant_textadds_show,
            "SHOW_M" => $quant_textadds_show_m,
        );

        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where member_id='$member_id' Order By `text_ad_id` Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['text_ad_id'];
                $title = $this->dec ($row['title']);
                $displayed = $row['displayed'];

                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' border='0' ></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('{$dict['TD_del']}');\"><img src='./images/trash.png' border='0' alt='Delete'></a>";
                
                $content = getTextAdContent ($id);
                
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_TITLE" => $title,
                    "ROW_CONTENT" => $content,
                    "ROW_DISPLAYED" => $displayed,
                    
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $this->data ['TABLE_EMPTY'][] = array (
                "_" => "_"
            );
        }
    }

    //--------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/tad_details.tpl";
        $id = $this->GetGP ("id");

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where `text_ad_id`='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='25' style='width: 300px;'>";
                $description1 = "<input type='text' name='description1' value='".$row["description1"]."' maxlength='35' style='width: 200px;'>";
                $description2 = "<input type='text' name='description2' value='".$row["description2"]."' maxlength='35' style='width: 200px;'>";
                $url = "<input type='text' name='url' value='".$row["url"]."' maxlength='250' style='width: 300px;'>";
                $show_url = $row["show_url"];
                

            break;

            case FORM_FROM_GP:

                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='25' style='width: 300px;'>";
                $description1 = "<input type='text' name='description1' value='".$this->GetGP ("description1")."' maxlength='35' style='width: 200px;'>";
                $description2 = "<input type='text' name='description2' value='".$this->GetGP ("description2")."' maxlength='35' style='width: 200px;'>";
                $url = "<input type='text' name='url' value='".$this->GetGP ("url")."' maxlength='250' style='width: 300px;'>";
                $show_url = $this->GetGP ("show_url");
                
            break;

            case FORM_EMPTY:
            default:

                $title = "<input type='text' name='title' value='' maxlength='25' style='width: 300px;'>";
                $description1 = "<input type='text' name='description1' value='' maxlength='35' style='width: 200px;'>";
                $description2 = "<input type='text' name='description2' value='' maxlength='35' style='width: 200px;'>";
                $url = "<input type='text' name='url' value='http://' maxlength='250' style='width: 300px;'>";
                $show_url = 0;

            break;
        }

        $checked = ($show_url == 1)? "checked" : "";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),

            "MAIN_DESCRIPTION1" => $description1,
            "MAIN_DESCRIPTION2" => $description2,
            
            "MAIN_URL" => $url,
            "MAIN_URL_ERROR" => $this->GetError ("url"),

            "MAIN_CHECKED" => $checked,

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['TD_pageTitle1'];
        $this->pageHeader = $dict['TD_pageTitle1'];
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        GLOBAL $dict;
        
        $member_id = $this->member_id;
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
        $quant_textadds = $this->db->GetSetting ("quant_textadds");
        if ($total >= $quant_textadds) $this->Redirect ($this->pageUrl);
         
        $this->pageTitle = $dict['TD_pageTitle1'];
        $this->pageHeader = $dict['TD_pageTitle1'];
        
        $title = $this->enc ($this->GetValidGP ("title", $dict['TD_Title'], VALIDATE_NOT_EMPTY));
        $description1 = $this->enc ($this->GetGP ("description1"));
        $description2 = $this->enc ($this->GetGP ("description2"));
        $url = $this->enc ($this->GetValidGP ("url", "URL", VALIDATE_URL));
        $show_url = $this->GetGP ("show_url");

        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (`title`, `description1`, `description2`, `url`, `member_id`, `show_url`) values ('$title', '$description1', '$description2', '$url', '$member_id', '$show_url')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        GLOBAL $dict;
        $this->pageTitle = $dict['TD_pageTitle2'];
        $this->pageHeader = $dict['TD_pageTitle2'];
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        GLOBAL $dict;
        $this->pageTitle = $dict['TD_pageTitle2'];
        $this->pageHeader = $dict['TD_pageTitle2'];
        $id = $this->GetGP ("id");
        $member_id = $this->member_id;
        
        $title = $this->enc ($this->GetValidGP ("title", $dict['TD_Title'], VALIDATE_NOT_EMPTY));
        $description1 = $this->enc ($this->GetGP ("description1"));
        $description2 = $this->enc ($this->GetGP ("description2"));
        $url = $this->enc ($this->GetValidGP ("url", "URL", VALIDATE_URL));
        $show_url = $this->GetGP ("show_url");

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', description1='$description1', description2='$description2', url='$url', show_url='$show_url' Where text_ad_id='$id' And member_id='$member_id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->member_id;
        $this->db->ExecuteSql ("Delete From {$this->object} Where text_ad_id='$id' And member_id='$member_id'");
        $this->Redirect ($this->pageUrl);
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("text_ads");

$zPage->Render ();

?>
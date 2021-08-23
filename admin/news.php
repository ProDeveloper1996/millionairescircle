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
        $this->orderDefault = "news_date";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/news.tpl";
        $this->pageTitle = "News";
        $this->pageHeader = "News";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='Add a news'><img src='./images/add.png' border='0'></a>",
            "HEAD_DATE" => $this->Header_GetSortLink ("news_date", "Date"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("title", "Title"),
            "HEAD_DESCRIPTION" => $this->Header_GetSortLink ("description", "Text"),
            "HEAD_DESTINATION" => $this->Header_GetSortLink ("destination", "Visible for"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['news_id'];
                $title = $this->dec ($row['title']);

                $date = date ("d-m-Y", $row['news_date']);
                $description = $this->dec($row['description']);
                $destination = ($row['destination'] == 1)? "Member Area" : "Public Area";
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"news_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' alt='Edit'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this news?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_TITLE" => $title,
                    "ROW_DATE" => $date,
                    "ROW_DESTINATION" => $destination,
                    "ROW_DESCRIPTION" => $description,
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
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
        $this->mainTemplate = "./templates/news_details.tpl";
        $this->javaScripts = $this->GetJavaScript ();
        $id = $this->GetGP ("id");
        $res_0 = "";
        $res_1 = "";
        switch ($source)
        {
            case FORM_FROM_DB:
                $row = $this->db->GetEntry ("Select * From {$this->object} Where news_id=$id", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 594px;'>";
                $date = getDaySelect (date ("d", $row["news_date"]), "dateDay") . getMonthSelect (date ("m", $row["news_date"]), "dateMonth") . getYearSelect (date ("Y", $row["news_date"]), "dateYear");
                $article = "<textarea name='article' rows='6' style='width: 594px;'>".$row["article"]."</textarea>";

                //$description = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["description"]."</textarea>";
                $description = $this->FCKeditor( "description", htmlspecialchars_decode($row["description"]) );

                $check = $row["destination"];
                if ($check == 0) $res_0 = "checked";
                if ($check == 1) $res_1 = "checked";
                $check ="<input type=radio name=check value=0 $res_0> Public area &nbsp;&nbsp;<input type=radio name=check value=1 $res_1> Members Area";
                $photo = "";
                if ($row["photo"] != "" And file_exists ("../data/news/".$row["photo"].".jpg"))
                {
                    $photo = "<a href='../data/news/".$row["photo"].".jpg' target='_blank'><img title='Enlarge' border='0' src='../data/news/small_".$row["photo"].".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' png='Delete Photo'></a>";

                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }

                break;

            case FORM_FROM_GP:
                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 594px;'>";
                $date = getDaySelect ($this->GetGP ("dateDay"), "dateDay") . getMonthSelect ($this->GetGP ("dateMonth"), "dateMonth") . getYearSelect ($this->GetGP ("dateYear"), "dateYear");
                $article = "<textarea name='article' rows='6' style='width: 594px;'>".$this->GetGP ("article")."</textarea>";
                //$description = "<textarea rows='14' style='width: 594px; height: 340px;' id='content' name='content'>".$this->GetGP ("description")."</textarea>";
                $description = $this->FCKeditor( "description", htmlspecialchars_decode($this->GetGP ("description")) );

                $check = $this->GetGP ("check");
                if ($check == 0) $res_0 = "checked";
                if ($check == 1) $res_1 = "checked";
                $check ="<input type=radio name=check value=0 $res_0> Public area &nbsp;&nbsp;<input type=radio name=check value=1 $res_1> Members Area";
                $photo = "";
                $photo_file = $this->db->GetOne ("Select photo From `{$this->object}` Where news_id=$id");

                if ($photo_file != "" And file_exists ("../data/news/".$photo_file.".jpg") And $opCode == "update")
                {
                    $photo = "<a href='../data/news/".$photo_file.".jpg' target='_blank'><img title='Enlarge' border='0' src='../data/news/small_".$photo_file.".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' title='Delete Photo'></a>";
                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                break;

            case FORM_EMPTY:
            default:
                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 594px;'>";
                $date = getDaySelect ("", "dateDay") ." ". getMonthSelect ("", "dateMonth") ." ". getYearSelect ("", "dateYear");
                $article = "<textarea name='article' rows='6' style='width: 594px;'></textarea>";
                //$description = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $description = $this->FCKeditor( "description", '' );
                $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                $check ="<input type=radio name=check value=0 checked> Public area&nbsp;&nbsp;<input type=radio name=check value=1> Members Area";
                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),
            "MAIN_DATE" => $date,
            "MAIN_ARTICLE" => $article,
            "MAIN_CONTENT" => $description,
            "MAIN_PHOTO" => $photo,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_CHECK" => $check,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Add a news";
        $this->pageHeader = "<a href='{$this->pageUrl}'>News</a> / Add a news";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "Add a news";
        $this->pageHeader = "<a href='{$this->pageUrl}'>News</a> / Add a news";

        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $date = mktime (0, 0, 0, $this->GetGP ("dateMonth"), $this->GetGP ("dateDay"), $this->GetGP ("dateYear"));
            $article = $this->enc ($this->GetGP ("article"));
            $description = $this->enc ($this->GetGP ("description"));
            $check = $this->GetGP ("check");
            $this->db->ExecuteSql ("Insert into {$this->object} (news_date, destination, title, article, description, is_active) values ('$date', '$check', '$title', '$article', '$description', '1')");
            $id = $this->db->GetInsertID ();
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $symbs = getUnID (5);
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $short_name = $id."_".$symbs;
                $new_name = $short_name;
                $thumb_name = "small_".$new_name;

                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($_FILES['photo']['name'], "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/news/".$new_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/news/".$new_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/news/".$new_name, 0644);
                    copy ($physical_path."data/news/".$new_name.".jpg", $physical_path."data/news/".$thumb_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/news/".$thumb_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/news/".$thumb_name.".jpg", 0644);
                    makeThumbnail ($physical_path."data/news/".$thumb_name.".jpg", 0);
                    makeThumbnail ($physical_path."data/news/".$new_name.".jpg", 1);
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where news_id='$id'");
                }
            }
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit news";
        $this->pageHeader = "<a href='{$this->pageUrl}'>News</a> / Edit news";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "To edit news";
        $this->pageHeader = "<a href='{$this->pageUrl}'>News</a> / Edit news";
        $id = $this->GetGP ("id");
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $date = mktime (0, 0, 0, $this->GetGP ("dateMonth"), $this->GetGP ("dateDay"), $this->GetGP ("dateYear"));
            $article = $this->enc ($this->GetGP ("article"));
            $description = $this->enc ($this->GetGP ("description"));
            $check = $this->GetGP ("check");

            $this->db->ExecuteSql ("Update {$this->object} Set news_date='$date', title='$title', destination='$check', article='$article', description='$description' Where news_id=$id");

            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $symbs = getUnID (5);
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $short_name = $id."_".$symbs;
                $new_name = $short_name;
                $thumb_name = "small_".$new_name;

                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($_FILES['photo']['name'], "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/news/".$new_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/news/".$new_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/news/".$new_name.".jpg", 0644);
                    copy ($physical_path."data/news/".$new_name.".jpg", $physical_path."data/news/".$thumb_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/news/".$thumb_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/news/".$thumb_name.".jpg", 0644);
                    makeThumbnail ($physical_path."data/news/".$thumb_name.".jpg", 0);
                    makeThumbnail ($physical_path."data/news/".$new_name.".jpg", 1);
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where news_id='$id'");
                }
            }
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where news_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where news_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/news/".$filename.".jpg"))) unlink ($physical_path."data/news/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/news/small_".$filename.".jpg"))) unlink ($physical_path."data/news/small_".$filename.".jpg");
        $this->db->ExecuteSql ("Delete From {$this->object} Where news_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_delphoto ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where news_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/news/".$filename.".jpg"))) unlink ($physical_path."data/news/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/news/small_".$filename.".jpg"))) unlink ($physical_path."data/news/small_".$filename.".jpg");
        $this->db->ExecuteSql ("Update {$this->object} Set photo='' Where news_id='$id'");
        $this->Redirect ($this->pageUrl."?ocd=edit&id=$id");
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

$zPage = new ZPage ("news");

$zPage->Render ();

?>
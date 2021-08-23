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
        $this->orderDefault = "number";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/testimonials.tpl";
        $this->pageTitle = "Testimonials";
        $this->pageHeader = "Testimonials";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='To add a testimonial'><img src='./images/add.png' border='0'></a>",
            "HEAD_AUTHOR" => "Author",
            "HEAD_DESCRIPTION" => "Testimonials",
            "HEAD_SEC" => "Order",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By number", true);
            $n = 0;
            while ($row = $this->db->FetchInArray ($result))
            {
                $n++;
                $id = $row['testimonial_id'];
                $author = $this->dec ($row['author']);
                $location = $this->dec ($row['location']);
                $number = $this->dec ($row['number']);
                if ($total == 1)
                {
                    $orderLink = "&nbsp;";    
                }
                elseif ($number == $total)
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Move Up'></a>";
                }
                elseif ($number == 1)
                {
                     $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' title='Move Down'></a>";
                }
                else
                {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Move Up'></a>";
                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' title='Move Down'></a>";
                }
                $description = nl2br ($this->dec ($row['description']));
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"testimonial_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' alt='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' title='Edit'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this testimonial?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_AUTHOR" => $author." (".$location.")",
                    "ROW_DESCRIPTION" => ($description != "")? $description : "&nbsp;",
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_ORDER" => $orderLink,
                    "ROW_SEC" => $n,
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor
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
        $this->mainTemplate = "./templates/testimonial_details.tpl";
        $id = $this->GetGP ("id");
        switch ($source)
        {
            case FORM_FROM_DB:
                $row = $this->db->GetEntry ("Select * From {$this->object} Where testimonial_id=$id", $this->pageUrl);
                $author = "<input type='text' name='author' value='".$row["author"]."' maxlength='120' style='width: 300px;'>";
                $location = "<input type='text' name='location' value='".$row["location"]."' maxlength='120' style='width: 300px;'>";
                $description = "<textarea name='description' rows='6' style='width: 520px;'>".$row["description"]."</textarea>";
                $photo = "";
                if ($row["photo"] != "" And file_exists ("../data/testimonials/".$row["photo"].".jpg"))
                {
                    $photo = "<a href='../data/testimonials/".$row["photo"].".jpg' target='_blank'><img title='Enlarge' border='0' src='../data/testimonials/small_".$row["photo"].".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' title='Delete Photo'></a>";

                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                break;

            case FORM_FROM_GP:
                $author = "<input type='text' name='author' value='".$this->GetGP ("author")."' maxlength='120' style='width: 300px;'>";
                $location = "<input type='text' name='location' value='".$this->GetGP ("location")."' maxlength='120' style='width: 300px;'>";
                $description = "<textarea name='description' rows='6' style='width: 520px;'>".$this->GetGP ("description")."</textarea>";
                $photo = "";
                $photo_file = $this->db->GetOne ("Select photo From {$this->object} Where testimonial_id=$id");

                if ($photo_file != "" And file_exists ("../data/testimonials/".$photo_file.".jpg"))
                {
                    $photo = "<a href='../data/testimonials/".$photo_file.".jpg' target='_blank'><img title='Enlarge' border='0' src='../data/testimonials/small_".$photo_file.".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' title='Delete Photo'></a>";

                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                break;

            case FORM_EMPTY:
            default:
                $author = "<input type='text' name='author' value='' maxlength='120' style='width: 300px;'>";
                $location = "<input type='text' name='location' value='' maxlength='120' style='width: 300px;'>";
                $description = "<textarea name='description' rows='6' style='width: 520px;'></textarea>";
                $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_AUTHOR" => $author,
            "MAIN_AUTHOR_ERROR" => $this->GetError ("author"),
            "MAIN_LOCATION" => $location,
            "MAIN_LOCATION_ERROR" => $this->GetError ("location"),
            "MAIN_DESCRIPTION" => $description,
            "MAIN_DESCRIPTION_ERROR" => $this->GetError ("description"),
            "MAIN_PHOTO" => $photo,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "To add a testimonial";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Testimonials</a> / To add a testimonial";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "To add a testimonial";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Testimonials</a> / To add a testimonial";

        $author = $this->enc ($this->GetValidGP ("author", "Author", VALIDATE_NOT_EMPTY));
        $location = $this->enc ($this->GetValidGP ("location", "Location", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("description", "Description", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $number = $this->db->GetOne("Select MAX(number) From {$this->object}", 0) + 1;
            $this->db->ExecuteSql ("Insert into {$this->object} (author, number, location, description, is_active) values ('$author', '$number', '$location', '$description', 1)");
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
                $ext = getExtension ($oldname, "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/testimonials/".$new_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/testimonials/".$new_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/testimonials/".$new_name, 0644);
                    copy ($physical_path."data/testimonials/".$new_name.".jpg", $physical_path."data/testimonials/".$thumb_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/testimonials/".$thumb_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/testimonials/".$thumb_name.".jpg", 0644);
                    makeThumbnail ($physical_path."data/testimonials/".$thumb_name.".jpg", 0);
                    makeThumbnail ($physical_path."data/testimonials/".$new_name.".jpg", 1);
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where testimonial_id='$id'");
                }
            }
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "To edit testimonial";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Testimonials</a> / To edit testimonial";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "To edit testimonial";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Testimonials</a> / To edit testimonial";
        $id = $this->GetGP ("id");
        $author = $this->enc ($this->GetValidGP ("author", "Author", VALIDATE_NOT_EMPTY));
        $location = $this->enc ($this->GetValidGP ("location", "Location", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("description", "Description", VALIDATE_NOT_EMPTY));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update {$this->object} Set author='$author', location='$location', description='$description' Where testimonial_id=$id");
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
                $ext = getExtension ($oldname, "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/testimonials/".$new_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/testimonials/".$new_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/testimonials/".$new_name.".jpg", 0644);
                    copy ($physical_path."data/testimonials/".$new_name.".jpg", $physical_path."data/testimonials/".$thumb_name.".jpg");
                    $cmd = "chmod 666 ".$physical_path."data/testimonials/".$thumb_name.".jpg";
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/testimonials/".$thumb_name.".jpg", 0644);
                    makeThumbnail ($physical_path."data/testimonials/".$thumb_name.".jpg", 0);
                    makeThumbnail ($physical_path."data/testimonials/".$new_name.".jpg", 1);
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where testimonial_id='$id'");
                }
            }
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where testimonial_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where testimonial_id='$id'");
        $number = $this->db->GetOne ("Select number From `{$this->object}` Where testimonial_id='$id'");

        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/testimonials/".$filename.".jpg"))) unlink ($physical_path."data/testimonials/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/testimonials/small_".$filename.".jpg"))) unlink ($physical_path."data/testimonials/small_".$filename.".jpg");
        $this->db->ExecuteSql ("Delete From {$this->object} Where testimonial_id='$id'");

        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where number>'$number'", 0);
        if ($total > 0) $this->db->ExecuteSql ("Update `{$this->object}` Set number=number-1 Where number>'$number'");

        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_delphoto ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where testimonial_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/testimonials/".$filename.".jpg"))) unlink ($physical_path."data/testimonials/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/testimonials/small_".$filename.".jpg"))) unlink ($physical_path."data/testimonials/small_".$filename.".jpg");
        $this->db->ExecuteSql ("Update {$this->object} Set photo='' Where testimonial_id='$id'");
        $this->Redirect ($this->pageUrl."?ocd=edit&id=$id");
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select number From {$this->object} Where testimonial_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select testimonial_id From {$this->object} Where number='$number_next'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set number=number+1 Where testimonial_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set number=number-1 Where testimonial_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select number From {$this->object} Where testimonial_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne ("Select testimonial_id From {$this->object} Where number='$number_next'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set number=number-1 Where testimonial_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set number=number+1 Where testimonial_id='$id_next'");
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

$zPage = new ZPage ("testimonials");

$zPage->Render ();

?>
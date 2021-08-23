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
        $this->mainTemplate = "./templates/lands.tpl";
        $this->pageTitle = "Landing Pages";
        $this->pageHeader = "Landing Pages";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='Add a landing page'><img src='./images/add.png' border='0'></a>",
            
            "HEAD_DATE" => $this->Header_GetSortLink ("z_date", "Created on"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("title", "Landing Page Title"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
            $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='1'", 1);

            $thisSiteUrl = $this->db->GetSetting ("SiteUrl");
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By z_date Asc", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['land_id'];
                $title = $this->dec ($row['title']);
                $date = date ("d-m-Y", $row['z_date']);

                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"land_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='24' border='0' title='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='24' border='0' title='Edit Page'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this page?');\"><img src='./images/trash.png' width='24' border='0' title='Delete Page'></a>";
                
                $landLink = "<a href='".$thisSiteUrl."land.php?id=".$id."&ref=$ref_id' target='blank'><img src='./images/view.png' border='0' title='View Page in new tab' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_TITLE" => $title,
                    "ROW_DATE" => $date,
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_EDITLINK" => $editLink,
                    "ROW_LANDLINK" => $landLink,
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
        $this->mainTemplate = "./templates/land_details.tpl";
        $this->javaScripts = $this->GetJavaScript ();
        $id = $this->GetGP ("id");

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where land_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                //$description = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["description"]."</textarea>";
                $description = $this->FCKeditor( "content", htmlspecialchars_decode( $row["description"] ) );
                
                $photo = "";
                if ($row["photo"] != "" And file_exists ("../data/lands/".$row["photo"]))
                {
                    $photo = "<img title='".$row["title"]."' border='0' src='../data/lands/".$row["photo"]."'>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' alt='Delete Photo'></a>";

                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }

                break;

            case FORM_FROM_GP:
                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                //$description = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$this->GetGP ("description")."</textarea>";
                $description = $this->FCKeditor( "content", htmlspecialchars_decode( $this->GetGP ("content") ) );
                
                $photo = "";
                $photo_file = $this->db->GetOne ("Select photo From `{$this->object}` Where land_id='$id'");

                if ($photo_file != "" And file_exists ("../data/lands/".$photo_file.".jpg") And $opCode == "update")
                {
                    $photo = "<img title='".$this->GetGP ("title")."' border='0' src='../data/lands/".$photo_file."'>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this photo?')\"><img src='./images/trash.png' border='0' alt='Delete Photo'></a>";
                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                
                break;

            case FORM_EMPTY:
            default:
                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                //$description = "<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $description = $this->FCKeditor( "content", '' );
                $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),
            "MAIN_CONTENT" => $description,
            "MAIN_PHOTO" => $photo,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Add Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Landing Pages</a> / Add Page";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "Add Page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Landing Pages</a> / Add Page";

        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("content", "Page Content", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (z_date, title, description, is_active) values ('".time ()."', '$title', '$description', '0')");
            $id = $this->db->GetInsertID ();
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $name = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $new_name = $id."_".$name;

                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($name, "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/lands/".$new_name);
                    $cmd = "chmod 666 ".$physical_path."data/lands/".$new_name;
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/lands/".$new_name, 0644);
                    
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$new_name' Where `land_id`='$id'");
                }
            }
            
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Landing Page edit page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Landing Pages</a> / Edit";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Landing Page edit page";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Landing Pages</a> / Edit";
        $id = $this->GetGP ("id");
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("content", "Page Content", VALIDATE_NOT_EMPTY));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', description='$description' Where land_id=$id");
            
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $name = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $new_name = $id."_".$name;
                
                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($name, "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/lands/".$new_name);
                    $cmd = "chmod 666 ".$physical_path."data/lands/".$new_name;
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."data/lands/".$new_name, 0644);
                    
                    $this->db->ExecuteSql ("Update {$this->object} Set photo='$new_name' Where `land_id`='$id'");
                }
            }
            
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where land_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where land_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/lands/".$filename))) unlink ($physical_path."data/lands/".$filename);
        
        $this->db->ExecuteSql ("Delete From {$this->object} Where land_id='$id'");
        $this->Redirect ($this->pageUrl);
    }
    
    //--------------------------------------------------------------------------
    function ocd_delphoto ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where land_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/lands/".$filename))) unlink ($physical_path."data/lands/".$filename);
        $this->db->ExecuteSql ("Update {$this->object} Set photo='' Where land_id='$id'");
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

$zPage = new ZPage ("lands");

$zPage->Render ();

?>
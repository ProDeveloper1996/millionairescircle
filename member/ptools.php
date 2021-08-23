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
		  $this->mainTemplate = "./templates/ptools.tpl";
        $this->pageTitle = $dict['PT_pageTitle'];
        $this->pageHeader = $dict['PT_pageTitle'];
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
        $useBanners = $this->db->GetSetting ("useBanners", "");
        if ($useBanners != "1") $this->Redirect ("overview.php"); 
        
        $member_id = $this->member_id;
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        
        $message = "";
        $ec = $this->GetGP ("ec", "");
        if ($ec == "add") $message = "<span class='message'>{$dict['PT_mess1']}</span>";
        if ($ec == "rem") $message = "<span class='message'>{$dict['PT_mess2']}</span>";
        if ($ec == "err") $message = "<span class='error'>{$dict['PT_mess3']}</span>";

        $total = $this->db->GetOne ("Select COUNT(*) From `{$this->object}` Where member_id='$member_id'", 0);
        //$ref_link = $siteUrl."index.php?spon=".$member_id;
        
        $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
        $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
        $ref_link = $siteUrl."?ref=".$ref_id;

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_CONFIRM" => $message,
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
            "PAGESELECT" => $this->PagePromoteSelect ($siteUrl, $ref_link), 
        );
       
        if ($total > 0)
        {
            
            $bgcolor = "#607083";
            $result = $this->db->ExecuteSql ("Select * From {$this->object}  Where member_id='$member_id' Order By `ptool_id` Asc", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ptool_id'];
                $title = $this->dec ($row['title']);
                $link = $row['link'];
                $photo = $row['photo'];
                $object = "<a href='$link' target='_blank'><img width='100%' src='".$siteUrl."data/ptools/".$photo."' alt='$title' border='0' title='$title' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('{$dict['PT_mess4']}');\"><img src='./images/trash.png' border='0' alt='{$dict['PT_Delete']}' title='{$dict['PT_Delete']}' /></a>";
                $bgcolor = ($bgcolor == "#607083") ? "" : "#607083";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_OBJECT" => $object,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor,
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
    function PagePromoteSelect ($siteUrl, $value)
    {
        $member_id = $this->member_id;
        $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
        $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
        $ref_link = $siteUrl."?ref=".$ref_id;
        $toRet = "<select name='link' style='width: 100%;'> \r\n";
        $selected = ($value == $ref_link) ? "selected" : "";
        $toRet .= "<option value='$ref_link' $selected>$ref_link</option>";
        
        $countLands = $this->db->GetOne ("Select COUNT(*) From `lands` Where `is_active`='1'", 0);
        $result = $this->db->ExecuteSql ("Select * From `lands` Where `is_active`='1' Order By z_date Asc");
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['land_id'];
            
            $link = ($countLands == 1)?  $siteUrl."land.php?ref=".$ref_id : $siteUrl."land.php?id=".$id."&ref=".$ref_id;
            
            $selected = ($value == $link) ? "selected" : "";
            $toRet .= "<option value='$link' $selected>$link</option>";
            
        }
        $this->db->FreeSqlResult ($result);
        
        
        return $toRet."</select>\r\n";
        
    }


    //--------------------------------------------------------------------------
    function ocd_add ()
    {
        GLOBAL $dict;
        $member_id = $this->member_id;
        
        $title = $this->enc ($this->GetValidGP ("title", $dict['PT_Title'], VALIDATE_NOT_EMPTY));
        $link = $this->GetGP ("link", "");
        if (!array_key_exists ("photo", $_FILES) Or $_FILES['photo']['error'] >= 3)
        {
            $this->SetError ("photo", $dict['PT_errorphoto']);
        }
        if ($this->errors['err_count'] == 0)
        {
            $types = $_FILES['photo']['type'];
            $types_array = explode("/", $types);
            if ($types_array [0] != "image") $this->SetError ("photo", $dict['PT_errorphoto1']);
            if ( strpos($_FILES['photo']['name'],'php')!==false ) $this->SetError ("photo", $dict['PT_errorphoto1']);
        }
        
                    
        if ($this->errors['err_count'] > 0)
        {
            $siteUrl = $this->db->GetSetting ("SiteUrl");
              
            $total = $this->db->GetOne ("Select COUNT(*) From `ptools` Where member_id='$member_id'", 0);
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                "TITLE" => $title,
                "TITLE_ERROR" => $this->GetError ("title"),
                
                "IMAGE_ERROR" => $this->GetError ("photo"),
                
                
                "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
                "PAGESELECT" => $this->PagePromoteSelect ($siteUrl, $link), 
            ); 
            if ($total > 0)
            {
            
                $bgcolor = "#87CEEB";
                $result = $this->db->ExecuteSql ("Select * From {$this->object}  Where member_id='$member_id' Order By `ptool_id` Asc", true);
                while ($row = $this->db->FetchInArray ($result))
                {
                    $id = $row['ptool_id'];
                    $title = $this->dec ($row['title']);
                    $link = $row['link'];
                    $photo = $row['photo'];
                    $object = "<a href='$link' target='_blank'><img src='".$siteUrl."data/ptools/".$photo."' alt='$title' border='0' title='$title' /></a>";
                    $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('{$dict['PT_mess4']}');\"><img src='./images/trash.png' border='0' alt='{$dict['PT_Delete']}' title='{$dict['PT_Delete']}' /></a>";
                    $bgcolor = ($bgcolor == "#87CEEB") ? "#00FFFF" : "#87CEEB";
                    $this->data ['TABLE_ROW'][] = array (
                        "ROW_OBJECT" => $object,
                        "ROW_DELLINK" => $delLink,
                        "ROW_BGCOLOR" => $bgcolor,
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
        else
        {
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $symbs = $member_id."_".getUnID (10);
                $ext = getExtension ($oldname, "jpg");
                $new_name = $symbs.".".$ext;
                if (is_uploaded_file ($tmp_name))
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."data/ptools/".$new_name);
                    //$this->db->ExecuteSql ("Insert Into {$this->object} (`member_id`, `title`, `link`, `photo`) Values ('$member_id', '$title', '$link', '$new_name')");
                    //$this->Redirect ($this->pageUrl."?ec=add"); 
                    
                    $info = getimagesize ($physical_path."data/ptools/".$new_name);
                    $width = $info [0];
                    $height = $info [1];
                    if ($width > 0 And $height > 0)
                    {
                        $this->db->ExecuteSql ("Insert Into {$this->object} (`member_id`, `title`, `link`, `photo`) Values ('$member_id', '$title', '$link', '$new_name')");
                        $this->Redirect ($this->pageUrl."?ec=add"); 
                    }
                    else
                    {
                        unlink ($physical_path."data/ptools/".$new_name);
                        $this->Redirect ($this->pageUrl."?ec=size");
                    }

                }
                else
                {
                    $this->Redirect ($this->pageUrl."?ec=err"); 
                }
            }
        }
    }
    
    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->member_id; 
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where `ptool_id`='$id' And `member_id`='$member_id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/ptools/".$filename))) unlink ($physical_path."data/ptools/".$filename);
        $this->db->ExecuteSql ("Delete From {$this->object} Where ptool_id='$id' And `member_id`='$member_id'");
        $this->Redirect ($this->pageUrl."?ec=rem");
    }
    
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("ptools");

$zPage->Render ();

?>


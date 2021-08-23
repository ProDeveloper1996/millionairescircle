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
        $this->mainTemplate = "./templates/aptools.tpl";
        $this->pageTitle = "Admin Banners";
        $this->pageHeader = "Admin Banners";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            
            "HEAD_ID" => "ID",
            "HEAD_PHOTO" => "Banner",
            "HEAD_TITLE" => "Title",
            "MAIN_ACTION" => $this->pageUrl,
			"MAIN_ERROR" => ($this->GetGP ("ile", "") == "") ? "" : "<span style='color:red;'>Image loading error</span>",
            
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $thisSiteUrl = $this->db->GetSetting ("SiteUrl");
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By `aptool_id` Asc", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['aptool_id'];
                $photo = $row['photo'];
                $title = $this->dec ($row['title']);
                
                $photo = "<img width='100%' src='../data/aptools/".$photo."' />";

                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"aptool_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' border='0' alt='Change activity status' title='Change activity status'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this banner?');\"><img src='./images/trash.png' border='0' alt='Delete banner' title='Delete banner'></a>";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $id,
                    "ROW_PHOTO" => $photo,
                    "ROW_TITLE" => $title,
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
            
        $title = $this->enc ($this->GetGP ("title", ""));
		$sError='?ile=1';
        if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3 And $title != "")
        {
            $name = $_FILES['photo']['name'];
            $tmp_name = $_FILES['photo']['tmp_name'];
            
            $types = $_FILES['photo']['type'];
            $types_array = explode("/", $types);
            if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                $this->Redirect ($this->pageUrl.$sError);
                exit();
            }

            $symbs = getUnID (12);
            $ext = getExtension ($name, "jpg");
	$whitelist = array("jpg","jpeg","gif","png");
            $new_name = $symbs.".".$ext;

            if(in_array($ext, $whitelist))
	{
		$imageinfo = @getimagesize($_FILES['photo']['tmp_name']);
		if($imageinfo['mime'] == 'image/gif' || $imageinfo['mime'] == 'image/jpeg' || $imageinfo['mime']== 'image/jpg' || $imageinfo['mime'] == 'image/png')
		{
			if (is_uploaded_file ($tmp_name))
			{
				$physical_path = $this->db->GetSetting ("PathSite");
				move_uploaded_file ($tmp_name, $physical_path."data/aptools/".$new_name);
				$cmd = "chmod 666 ".$physical_path."data/aptools/".$new_name;
				@exec ($cmd, $output, $retval);
				@chmod ($physical_path."data/aptools/".$new_name, 0644);
					
				$this->db->ExecuteSql ("Insert into {$this->object} (photo, title, is_active) values ('$new_name', '$title', '0')");
				$sError='';
			}   
		}
	}
        }
        $this->Redirect ($this->pageUrl.$sError);
        
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where aptool_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where aptool_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/aptools/".$filename))) unlink ($physical_path."data/aptools/".$filename);
        
        $this->db->ExecuteSql ("Delete From {$this->object} Where aptool_id='$id'");
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

$zPage = new ZPage ("aptools");

$zPage->Render ();

?>
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
            
            "HEAD_ID" => "#",
            "HEAD_PHOTO" => "Banner image",
            "HEAD_TITLE" => "Admin Banner Title",
            "MAIN_ACTION" => $this->pageUrl,
            
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
                
                $photo = "<img src='../data/aptools/".$photo."' />";

                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"aptool_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='24' border='0' title='Change activity status'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this banner?');\"><img src='./images/trash.png' width='24' border='0' title='Delete Banner'></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";

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
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
            
        $title = $this->enc ($this->GetGP ("title", ""));
        if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3 And $title != "")
        {
            $name = $_FILES['photo']['name'];
            $tmp_name = $_FILES['photo']['tmp_name'];
            
            $symbs = getUnID (12);
            $ext = getExtension ($name, "jpg");
            
            $new_name = $symbs.".".$ext;
             
            if (is_uploaded_file ($tmp_name))
            {
                $physical_path = $this->db->GetSetting ("PathSite");
                move_uploaded_file ($tmp_name, $physical_path."data/aptools/".$new_name);
                $cmd = "chmod 666 ".$physical_path."data/aptools/".$new_name;
                @exec ($cmd, $output, $retval);
                @chmod ($physical_path."data/aptools/".$new_name, 0644);
                    
                $this->db->ExecuteSql ("Insert into {$this->object} (photo, title, is_active) values ('$new_name', '$title', '0')");
           }
        }
        $this->Redirect ($this->pageUrl);
        
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
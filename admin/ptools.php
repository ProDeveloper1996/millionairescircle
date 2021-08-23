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
        $this->mainTemplate = "./templates/ptools.tpl";
        $this->pageTitle = "Members Banners";
        $this->pageHeader = "Members Banners";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        
        $message = "";
        $ec = $this->GetGP ("ec", "");
        if ($ec == "add") $message = "<span class='message'>The banner has been successfully added</span>";
        if ($ec == "rem") $message = "<span class='message'>The banner has been successfully removed</span>";
        if ($ec == "err") $message = "<span class='error'>Some error. Please try again later...</span>";

        $total = $this->db->GetOne ("Select COUNT(*) From `{$this->object}`", 0);
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_OWNER" => $this->Header_GetSortLink ("member_id", "Author"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
       
        $bgcolor = "";
        if ($total > 0)
        {
            
            
            $result = $this->db->ExecuteSql ("Select * From {$this->object}  Order By `ptool_id` Desc", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ptool_id'];
                $member_id = $row['member_id'];
                
                $name = $this->dec ($this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where `member_id`='$member_id'"));
                
                $title = $this->dec ($row['title']);
                $link = $row['link'];
                $photo = $row['photo'];
                
                $object = "<a href='$link' target='_blank'><img width='100%' src='".$siteUrl."data/ptools/".$photo."' border='0' title='$title' /></a>";
                
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this banner?');\"><img src='./images/trash.png' border='0' title='Delete banner' alt='Delete banner' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_OWNER" => $name." (#$member_id)",
                    "ROW_OBJECT" => $object,
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
                "ROW_BGCOLOR" => $bgcolor,
            );
       }
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where `ptool_id`='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/ptools/".$filename))) unlink ($physical_path."data/ptools/".$filename);
        $this->db->ExecuteSql ("Delete From {$this->object} Where ptool_id='$id'");
        $this->Redirect ($this->pageUrl);
    }
    
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("ptools");

$zPage->Render ();

?>


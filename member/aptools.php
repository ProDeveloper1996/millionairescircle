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
        
        $member_id = $this->member_id;
        $aptools = $this->db->GetOne ("Select Count(*) From `aptools` Where `is_active`='1'", 0);
        if ($aptools == 0) $this->Redirect ("overview.php");
        
        $this->mainTemplate = "./templates/aptools.tpl";
        $this->pageTitle = "Site Banners";
        $this->pageHeader = "Site Banners";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );
        $result = $this->db->ExecuteSql ("Select * From {$this->object} Where `is_active`='1' Order By `aptool_id` Asc");
        $bgcolor = "#607083";
        $SiteUrl = $this->db->GetSetting ("SiteUrl");
        while ($row = $this->db->FetchInArray ($result))
        {
            $photo = $row['photo'];
            $photoUrl = $SiteUrl."data/aptools/".$photo;
            $title = $this->dec ($row['title']);
            $photo = "<img width='100%' src='".$SiteUrl."data/aptools/".$photo."' border='0' title='$title' alt='$title' />";
            
            $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
            $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
            $url = $SiteUrl."?ref=".$ref_id;
            
            
            $bgcolor = ($bgcolor == "#607083") ? "" : "#607083";
            $this->data ['TABLE_ROW'][] = array (
                "ROW_PAGE" => $url, 
                "ROW_PHOTO" => $photo,
                "ROW_BGCOLOR" => $bgcolor,
                "ROW_TEXT" => $title,
                "ROW_TARGET_URL" => $url,
                "ROW_IMAGE_URL" => $photoUrl, 
            );
            
        }
        $this->db->FreeSqlResult ($result);
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("aptools");

$zPage->Render ();

?>
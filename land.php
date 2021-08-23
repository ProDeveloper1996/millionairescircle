<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/land.tpl";
        
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
		GLOBAL $dict;
		
        $title = $dict['LAND_LandingPage'];
        $this->pageTitle = $title;
        $this->pageHeader = $title;
        
        $land_id = $this->GetGP ("id", 0);
        
        $member_id = $this->GetGP ("ref",  '1' );
        $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
        if ( $ReferrerUrl=='username' ) {
            if ( $member_id=='1' ) $member_id=$this->db->GetOne ("Select username From `members` Where id='".(int)$member_id."'", 'admin');
            $member_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where username='$member_id'", 1);
        }

        $_SESSION['enroller'] = $member_id;
        $_SESSION['way']  = 0;
        
        if ($land_id == 0) $land_id = $this->db->GetOne ("Select `land_id` From `lands` Where `is_active`='1' Order By RAND() Limit 1", 0);
//        if ($land_id == 0) $this->Redirect ("index.php"); 
        
        $content = $this->dec ($this->db->GetOne ("Select `description` From `lands` Where land_id='$land_id' And is_active=1", ""));
        $photo = $this->db->GetOne ("Select `photo` From `lands` Where land_id='$land_id' And is_active=1", "");
        $link = ($photo != "")? "<a href='/?ref=$member_id'><img src='./data/lands/".$photo."' title='{$dict['LAND_SignUpNow']}' alt='{$dict['LAND_SignUpNow']}' border='0' /></a>" : "<a href='/?ref=$member_id'>{$dict['LAND_SignUpNow']}</a>";
        
                
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_LINK" => $link,
            "MAIN_CONTENT" => $content,
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("land");

$zPage->Render ();

?>
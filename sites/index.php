<?php


require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_site.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/index.tpl";

    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $member_id = $this->site_id;
        $p_id = $this->GetGp ("p_id", 0);
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        if ($p_id == 0)
        {
            $p_id = $this->db->GetOne ("Select replica_id From `replicas` Where member_id='$member_id' And is_active=1 Order By order_index ASC Limit 1", 0);
        }       
        
        $_SESSION["enroller"] =  $member_id ;

        $title = $this->dec ($this->db->GetOne ("Select title From `replicas` Where replica_id='$p_id' And member_id='$member_id' And is_active=1", ""));
        if ($title == "") $this->Redirect ($siteUrl);
        $content = $this->dec ($this->db->GetOne ("Select content From `replicas` Where replica_id='$p_id' And member_id='$member_id' And is_active=1", ""));
        $this->pageTitle = $title;
        $this->pageHeader = $title;
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("index");

$zPage->Render ();

?>
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
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $p_id = $this->GetID ("p_id");
        if ($p_id > 1)
        {
            $title = $this->dec ($this->db->GetOne ("Select title From `pages` Where page_id='$p_id' And is_active=1 And is_member=0", ""));
            $content = $this->dec ($this->db->GetOne ("Select content From `pages` Where page_id='$p_id' And is_active=1 And is_member=0", ""));
            if ($title == "") $this->Redirect ("index.php");
        }
        else
        {
            //$this->Redirect ("/admin/login.php");
        }
        $this->mainTemplate = "./templates/testi.tpl";
        //$this->pageTitle = $title;
        $this->pageHeader = "Testimonial";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            //"MAIN_CONTENT" => $content,
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("content");

$zPage->Render ();

?>
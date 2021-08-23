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
        $this->orderDefault = "Title";
        XPage::XPage ($object);

        $this->mainTemplate = "./templates/template_elements.tpl";
        $this->pageTitle = "Template Elements";
        $this->pageHeader = "Template Elements";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {

        $ec = $this->GetGP ("ec");
        $message = ($ec == "yes")? "Changes were successfully saved." : "";

        $FOOTER_CONTENT = $this->db->GetSetting ("FooterContent");

        $this->data = array (
            "ACTION_SCRIPT" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $message,
            "FOOTER_CONTENT" => $FOOTER_CONTENT,

        );
    }

    //--------------------------------------------------------------------------
    function ocd_updatefooter()
    {

        $FooterContent = $this->GetGP ("FooterContent", "");

        $this->db->SetSetting ("FooterContent", $FooterContent);
        
        $this->Redirect ($this->pageUrl."?ec=yes");

    }

    //--------------------------------------------------------------------------
    function ocd_updatelogo()
    {

            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $new_name = 'logo.png';

                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($_FILES['photo']['name'], "img");
                $whitelist = array("png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $physical_path = $this->db->GetSetting ("PathSite");
                    move_uploaded_file ($tmp_name, $physical_path."img/".$new_name);
                    $cmd = "chmod 666 ".$physical_path."img/".$new_name;
                    @exec ($cmd, $output, $retval);
                    @chmod ($physical_path."img/".$new_name, 0644);
                }
            }

        $this->Redirect ($this->pageUrl."?ec=yes");

    }


}

//------------------------------------------------------------------------------

$zPage = new ZPage ("template_elements");

$zPage->Render ();

?>
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
        GLOBAL $dict;
        
		  XPage::XPage ($object);
        $this->mainTemplate = "./templates/testimonials.tpl";
        $this->pageTitle = $dict['Testimonials'];
        $this->pageHeader = $dict['Testimonials'];
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        $bgcolor = "";
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where is_active=1", 0);
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_active=1 Order By number Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $author = $this->dec ($row['author']);
                $location = $this->dec ($row['location']);
                $description = nl2br ($this->dec ($row['description']));
                $photo = (strlen($row['photo']) > 0)? "<a href='./data/testimonials/".$row['photo'].".jpg' target='_blank'><img class='img_w_d_board' src='./data/testimonials/small_".$row['photo'].".jpg' width='120' border='0' align='left' vspace='5' hspace='5' alt='{$dict['Testimonials_Enlarge_Img']}'></a>" : "";
                $this->data ["TESTIMONIALS_ROW"][] = array (
                    "ROW_AUTHOR" => $author,
                    "ROW_LOCATION" => $location,
                    "ROW_DESCRIPTION" => $description,
                    "ROW_PHOTO" => $photo,
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TESTIMONIALS_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("testimonials");

$zPage->Render ();

?>
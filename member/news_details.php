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
		  XPage::XPage ($object);
        $this->mainTemplate = "./templates/news_details.tpl";
        $this->pageTitle = $dict['News_pageTitleDet'];
        $this->pageHeader = $dict['News_pageTitleDet'];
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
        $news_id = $this->GetID ("nid");
        $count = $this->db->GetOne ("Select Count(*) From `news` Where news_id='$news_id'", 0);
        if ($count != 1) $this->Redirect ("overview.php");
        $row = $this->db->GetEntry ("Select * From `news` Where news_id='$news_id'", "/.index.php");
        $description = $this->dec ($row['description']);
        $photo = "";
        if (strlen ($row['photo']) > 0)
        {
            $photo = "<a href='../data/news/".$row['photo'].".jpg' target='_blank'><img align='left' class='img_w_d_board' src='../data/news/small_".$row['photo'].".jpg' border='0' align='left' vspace='6' hspace='4'></a>&nbsp;&nbsp;";
        }
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_DATE" => date ("d.m.Y", $row['news_date']),
            "MAIN_TITLE" => $row['title'],
            "MAIN_PHOTO" => $photo,
//            "MAIN_ARTICLE" => nl2br ($this->dec ($row['article'])),
            "MAIN_DESCRIPTION" => $description,
        );

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("news_details");

$zPage->Render ();

?>
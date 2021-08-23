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
        $this->mainTemplate = "./templates/news.tpl";
        $this->pageTitle = $dict['News_pageTitle'];
        $this->pageHeader = $dict['News_pageTitle'];
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
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where destination=1 And is_active=1", 0);
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where destination=1 And is_active=1 Order By news_date Desc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $date = date('M-d-Y' ,$row['news_date']);
                $title = $row['title'];
                $article = $row['article'];
                $description = $this->dec($row['description']);
                $photo = (strlen($row['photo']) > 0)? "<a href='../data/news/".$row['photo'].".jpg' target='_blank'><img class='img_w_d_board' src='../data/news/small_".$row['photo'].".jpg' width='120' border='0' align='left' vspace='5' hspace='5' alt='{$dict['Clicktoenlarge']}'></a>" : "";
                $this->data ["NEWS_ROW"][] = array (
                    "ROW_DATE" => $date,
                    "ROW_TITLE" => $title,
                    "ROW_ARTICLE" => $article,
                    "ROW_DESCRIPTION" => $description,
                    "ROW_PHOTO" => $photo,
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['NEWS_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("news");

$zPage->Render ();

?>
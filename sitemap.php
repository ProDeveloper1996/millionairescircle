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
        GLOBAL $dict;
        
		  $this->pageTitle = $dict['SM_pageTitle'];
        $this->pageHeader = $dict['SM_pageTitle'];
        $this->mainTemplate = "./templates/site_map.tpl";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );
        $total = $this->db->GetOne ("Select Count(*) From `pages` Where is_active=1", 0);
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `pages` Where is_active=1 Order By order_index");
            while ($row = $this->db->FetchInArray ($result))
            {
                $title = $this->dec ($row['menu_title']);
                $id = $row['page_id'];
                $link = "content.php?p_id=".$id;
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_TITLE" => $title,
                    "ROW_LINK" => $link,
                );
            }
            $this->db->FreeSqlResult ($result);
        }

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("site_map");

$zPage->Render ();

?>
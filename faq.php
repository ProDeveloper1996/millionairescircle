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
        $this->mainTemplate = "./templates/faq.tpl";
        $this->pageTitle = $dict['FAQ_pageTitle'];
        $this->pageHeader = $dict['FAQ_pageTitle'];
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );
        $bgcolor = "";

        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where is_active=1", "0");
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_active=1 Order By faq_id");
            $i = 1;
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['faq_id'];
                $question = nl2br ($this->dec($row['question']));
                $this->data ["QUESTION_ROW"][] = array (
                    "ROW_QUESTION" => $question,
                    "ROW_ID" => $id,
                     "ROW_ANSWER" =>  "<span style='color:#bfcad9;'>".nl2br ($this->dec($row['answer']))."</a>"
                );
                $i += 1;
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['FAQ_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("faq");

$zPage->Render ();

?>
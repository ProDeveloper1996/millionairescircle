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
        $this->mainTemplate = "./templates/tads.tpl";
        $this->pageTitle = "Members Text Ads";
        $this->pageHeader = "Members Text Ads";
        $this->orderDefault = "text_ad_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        
        $message = "";
        $ec = $this->GetGP ("ec", "");
        if ($ec == "rem") $message = "<span class='message'>The text ad has been successfully removed</span>";

        $total = $this->db->GetOne ("Select COUNT(*) From `{$this->object}`", 0);
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,

            "HEAD_ID" => $this->Header_GetSortLink ("text_ad_id", "#"),
			"HEAD_MEMBER" => $this->Header_GetSortLink ("member_id", "Text Ad Author"),
			"HEAD_CONTENT" => $this->Header_GetSortLink ("title", "Content"),
			"HEAD_DISPLAYED" => $this->Header_GetSortLink ("displayed", "Displayed (times)"),
			
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
       
        $bgcolor = "";
        if ($total > 0)
        {
            
            
            $result = $this->db->ExecuteSql ("Select * From {$this->object}  Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['text_ad_id'];
                $member_id = $row['member_id'];
                
                $name = $this->dec ($this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where `member_id`='$member_id'"));
                
                $content = getTextAdContent ($id); 
                $displayed = $row['displayed'];
                
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this text ad?');\"><img src='./images/trash.png' border='0' title='Delete Text Ad' alt='Delete Text Ad' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $id,
                    "ROW_MEMBER" => $name." (#$member_id)",
                    "ROW_CONTENT" => $content,
					"ROW_DISPLAYED" => $displayed,
                    
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor,
                );
            }
            $this->db->FreeSqlResult ($result);
       }       
       else
       {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor,
            );
       }
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Delete From {$this->object} Where text_ad_id='$id'");
        $this->Redirect ($this->pageUrl);
    }
    
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("text_ads");

$zPage->Render ();

?>


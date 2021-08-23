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
        $this->orderDefault = "ticket_id";
        XPage::XPage ($object);
        $this->orderDir = "desc";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/ticket.tpl";
        $this->pageTitle = "Member Tickets list";
        $this->pageHeader = "Member Tickets list";
        $id_filter = ($this->GetGP ("ticketid", -1) != -1) ? $this->GetGP ("ticketid") : $this->GetStateValue ("ticketid", "");
        $this->SaveStateValue ("ticketid", $id_filter);
        $filter = (strlen ($id_filter) > 0 and $id_filter > 0) ? " And ticket_id='$id_filter'" : "";
        $activeStatus = ($this->GetGP ("activ", -2) != -2) ? $this->GetGP ("activ", 1) : $this->GetStateValue ("activ", 1);
        $this->SaveStateValue ("activ", $activeStatus);
        if ($activeStatus == -1) $activeStatusIN = "";
        elseif ($activeStatus == 0) $activeStatusIN = " And is_active='$activeStatus'";
        else $activeStatusIN = " And is_active='$activeStatus'";
        $filter .= (strlen ($activeStatus) > 0) ? $activeStatusIN : "";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where 1 $filter");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "ACTIVE_STATUS_FILTER" => getActiveTicketSelect ($activeStatus),
            "ID_FILTER" => "<input type='text' name='ticketid' value='$id_filter' style='width:60px;' maxlength='6'>",
            "HEAD_ID" => $this->Header_GetSortLink ("ticket_id", "ID"),
            "HEAD_NAME" => "Member",
            "HEAD_SUBJECT" => $this->Header_GetSortLink ("subject", "Subject"),
            "HEAD_DATA_CREATE" => $this->Header_GetSortLink ("date_create", "Date created"),
            "HEAD_LAST_UPDATE" => $this->Header_GetSortLink ("last_update", "Last update"),
            "HEAD_LAST_REPLIER" => "Last replier",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Where 1 $filter Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ticket_id'];
                $member = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name, ' (ID: ".$row['member_id'].")') From members Where member_id='".$row['member_id']."'", "n/a");
                $subject = ($row['is_read'] == 1) ? $row['subject'] : "<b>".$row['subject']."</b>";
                $date_create = date ("d/M/Y", $row['date_create']);
                $last_update = date ("d/M/Y", $row['last_update']);
                $last_replier = ($row['last_replier'] == 0) ? "Admin" : $member ;
                $activeLink = "<a href='{$this->pageUrl}?ocd=activate&id=$id'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change active status'></a>";
                $viewLink = "<a href='{$this->pageUrl}?ocd=view&id=$id'><img src='./images/view.png' width='25' border='0' title='View details'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this ticket?');\"><img src='./images/trash.png' width='25' title='Delete'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $id,
                    "ROW_MEMBER" => $member,
                    "ROW_SUBJECT" => $subject,
                    "ROW_DATA_CREATE" => $date_create,
                    "ROW_LAST_UPDATE" => $last_update,
                    "ROW_LAST_REPLIER" => $last_replier,
                    "ROW_ACTIVELINK" => $activeLink,
                    "ROW_EDITLINK" => $viewLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#1D9BDD" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_view ()
    {
        $id = $this->GetGP ("id");
        $this->mainTemplate = "./templates/ticket_details.tpl";
        $this->pageTitle = "Member Ticket list";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Ticket list</a> / Member Ticket view";
        $this->javaScripts = $this->GetJavaScript ();

        $result = $this->db->ExecuteSql ("Select * From {$this->object} Where ticket_id='$id'");
        if ($row = $this->db->FetchInArray ($result))
        {
            $subject = $row['subject'];
            $last_update = date ("d M Y h:i A", $row['last_update']);
            $date_create = date ("d M Y h:i A", $row['date_create']);
            $status = ($row['is_active'] == 1) ? "<span class='message'><b>Open</b></span>" : "<span class='error'><b>Close</b></span>";
        }
        $this->db->FreeSqlResult ($result);
        $this->db->ExecuteSql ("Update {$this->object} Set is_read='1' Where ticket_id='$id'");

        $message = "<textarea name='message' cols='90' rows='8'></textarea>";

        $total = $this->db->GetOne ("Select Count(*) From `ticket_messages` Where ticket_id='$id'");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_LAST_UPDATE" => $last_update,
            "MAIN_DATE_CREATE" => $date_create,
            "MAIN_SUBJECT" => $subject,
            "MAIN_STATUS" => $status,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_MESSAGE" => $message,
            "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => "answer",
            "MAIN_ID" => $id,
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `ticket_messages` Where ticket_id='$id' Order By date_post");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ticket_message_id'];

                $message = nl2br ($this->dec ($row['message']));
                $date_post = date ("d M Y h:i A", $row['date_post']);
                $message_from = ($row['message_from'] == 0) ? "Admin" : $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From members Where member_id='".$row['message_from']."'", "n/a")." (ID: ".$row['message_from'].")";
                
                $bgcolor = ($bgcolor == "") ? "#1D9BDD" : "";
            
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_FROM" => $message_from,
                    "ROW_DATE_POST" => $date_post,
                    "ROW_MESSAGE" => $message,
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#1D9BDD" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Create Member Ticket";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Ticket list</a> / Create Member Ticket";
        $this->mainTemplate = "./templates/new_ticket.tpl";
        $member_id = "<input type='text' name='member_id' value='' maxlength='50' style='width: 80px;'>";
        $subject = "<input type='text' name='subject' value='' maxlength='200' style='width: 440px;'>";
        $message = "<textarea name='message' cols='70' rows='18'></textarea>";

        $this->data = array (
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_MEMBER" => $member_id,
            "MAIN_MEMBER_ERROR" => $this->GetError ("member_id"),
            "MAIN_SUBJECT" => $subject,
            "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
            "MAIN_MESSAGE" => $message,
            "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => "insert",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "Create Member Ticket";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Member Ticket list</a> / Create Member Ticket";
        $member_id = $this->enc ($this->GetValidGP ("member_id", "Member ID", VALIDATE_INT_POSITIVE));
        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));
        if ($member_id == 0)
        {
            $this->SetError ("member_id", "'Member ID' should be more 0");
        }
        if ($this->errors['err_count'] > 0)
        {
            $this->pageTitle = "Create Member Ticket";
            $this->pageHeader = "<a href='{$this->pageUrl}'>Member Ticket list</a> / Create Member Ticket";
            $this->mainTemplate = "./templates/new_ticket.tpl";
            $this->data = array (
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_MEMBER" => "<input type='text' name='member_id' value='".$this->GetGP ("member_id")."' maxlength='50' style='width: 80px;'>",
                "MAIN_MEMBER_ERROR" => $this->GetError ("member_id"),
                "MAIN_SUBJECT" => "<input type='text' name='subject' value='".$this->GetGP ("subject")."' maxlength='200' style='width: 440px;'>",
                "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
                "MAIN_MESSAGE" => "<textarea name='message' cols='70' rows='18'>".$this->GetGP ("message")."</textarea>",
                "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
                "MAIN_CANCEL_URL" => $this->pageUrl,
                "MAIN_OCD" => "insert",
            );
        }
        else
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (member_id, subject, date_create, last_update, last_replier) values ('$member_id', '$subject', '".time()."', '".time()."', '$member_id')");
            $ticket_id = $this->db->GetInsertID ();
            $this->db->ExecuteSql ("Insert into `ticket_messages` (ticket_id, message_from, message_to, message, date_post) values ('$ticket_id', '$member_id', '0', '$message', '".time()."')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_answer ()
    {
        $id = $this->GetGP ("id");
        $message = $this->enc ($this->GetGP ("message"));
        $member_id = $this->db->GetOne ("Select member_id From {$this->object} Where ticket_id='$id'");
        $this->db->ExecuteSql ("Insert into `ticket_messages` (ticket_id, message_from, message_to, message, date_post) values ('$id', '0', '$member_id', '$message', '".time()."')");
        $this->db->ExecuteSql ("Update {$this->object} Set last_update='".time()."', last_replier='0' Where ticket_id='$id'");
        $siteTitle = $this->db->GetOne ("Select value From `settings` Where keyname='SiteTitle'");
        $ticket_email = $this->db->GetOne ("Select value From `settings` Where keyname='ContactEmail'");
        $member_email = $this->db->GetOne ("Select email From `members` Where member_id='$member_id'");
        $email_headers = "From: $siteTitle <$ticket_email>\r\n";
        $subject = $this->db->GetOne("Select subject From {$this->object} Where ticket_id='$id'");
        $email_subject = "$siteTitle : New answer from admin";
            $email_body = "\r\nOn your ticket $subject, was received new answer.\r\n".
            "\r\n\r\n---------------------\r\n$siteTitle";
        @mail ($member_email, $email_subject, $email_body, $email_headers);
        $this->Redirect ($this->pageUrl."?ocd=view&id=$id");
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(Case When is_active=1 Then 0 Else 1 End) Where ticket_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id");
        $count = $this->db->GetOne ("Select Count(*) From {$this->object} Where ticket_id='$id'");
        if ($count > 0)
        {
            $this->db->ExecuteSql ("Delete From {$this->object} Where ticket_id='$id'");
            $this->db->ExecuteSql ("Delete From `ticket_messages` Where ticket_id='$id'");
        }
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script type="text/javascript" language="JavaScript">
        <!--
            function func ()
            {
                var mes = document.form1['message'].value;
                if (mes == '') {
                    document.getElementById ('error').innerText = 'You should specify Message';
                    return false;
                }
                else {
                    return true;
                }
            }
        -->
        </script>

_ENDOFJS_;
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("tickets");

$zPage->Render ();

?>
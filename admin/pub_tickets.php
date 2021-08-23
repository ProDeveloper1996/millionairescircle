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
        $this->orderDefault = "pub_ticket_id";
        XPage::XPage ($object);
        $this->orderDir = "desc";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/pub_tickets.tpl";
        $this->pageTitle = "Public Tickets list";
        $this->pageHeader = "Public Tickets list";

        $id_filter = ($this->GetGP ("ticketid", -1) != -1) ? $this->GetGP ("ticketid") : $this->GetStateValue ("ticketid", "");
        $this->SaveStateValue ("ticketid", $id_filter);
        $filter = (strlen ($id_filter) > 0 and $id_filter > 0) ? " And pub_ticket_id='$id_filter'" : "";

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
            "HEAD_ID" => $this->Header_GetSortLink ("pub_ticket_id", "ID"),
            "HEAD_NAME" => "Last Name",
            "HEAD_SUBJECT" => $this->Header_GetSortLink ("subject", "Subject"),
            "HEAD_EMAIL" => $this->Header_GetSortLink ("email", "Email"),
            "HEAD_TICKET_CODE" => "Ticket Code",
            "HEAD_DATA_CREATE" => $this->Header_GetSortLink ("date_create", "Created on"),
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
                $id = $row['pub_ticket_id'];
                $name = $row['last_name'];
                $email = $row['email'];
                $email = "<a href='mailto:$email'>$email</a>";
                $ticket_code = $row['ticket_code'];
                $subject = ($row['is_read'] == 1) ? $row['subject'] : "<font color='#0000FF'><b>".$row['subject']."</b></font>";
                $date_create = date ("d/M/Y", $row['date_create']);
                $last_update = date ("d/M/Y", $row['last_update']);
                $activeLink = "<a href='{$this->pageUrl}?ocd=activate&id=$id'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change active status'></a>";
                $viewLink = "<a href='{$this->pageUrl}?ocd=view&id=$id'><img src='./images/view.png' width='25' border='0' title='View Details'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this record?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $id,
                    "ROW_NAME" => $name,
                    "ROW_SUBJECT" => $subject,
                    "ROW_EMAIL" => $email,
                    "ROW_TICKET_CODE" => $ticket_code,
                    "ROW_DATA_CREATE" => $date_create,
                    "ROW_LAST_UPDATE" => $last_update,
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
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
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
        $this->pageTitle = "Public Ticket list";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Public Ticket list</a> / Public Ticket Details";
        $this->javaScripts = $this->GetJavaScript ();

        $result = $this->db->ExecuteSql ("Select * From {$this->object} Where pub_ticket_id='$id'");
        if ($row = $this->db->FetchInArray ($result))
        {
            $subject = $row['subject'];
            $last_update = date ("d M Y h:i A", $row['last_update']);
            $date_create = date ("d M Y h:i A", $row['date_create']);
            $status = ($row['is_active'] == 1) ? "<span class='message'><b>Open</b></span>" : "<span class='error'><b>Closed</b></span>";
        }
        $this->db->FreeSqlResult ($result);
        $this->db->ExecuteSql ("Update {$this->object} Set is_read='1' Where pub_ticket_id='$id'");
        $message = "<textarea name='message' cols='90' rows='8'></textarea>";
        $total = $this->db->GetOne ("Select Count(*) From `pub_ticket_messages` Where pub_ticket_id='$id'");
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
            $result = $this->db->ExecuteSql ("Select * From `pub_ticket_messages` Where pub_ticket_id='$id' Order By date_post");
            while ($row = $this->db->FetchInArray ($result))
            {
                $idt = $row['pub_ticket_message_id'];

                $message = nl2br ($this->dec ($row['message']));
                $date_post = date ("d M Y h:i A", $row['date_post']);
                $adm = $this->db->GetOne ("Select support_message from `pub_ticket_messages` Where pub_ticket_message_id='$idt'");
                $f_name = $this->db->GetOne ("Select first_name from `pub_tickets` Where pub_ticket_id='$id'");
                $l_name = $this->db->GetOne ("Select last_name from `pub_tickets` Where pub_ticket_id='$id'");

                $message_from = ($adm == 0)? $f_name." ".$l_name." ".$this->db->GetOne ("Select email from `pub_tickets` Where pub_ticket_id='$id'") : "Admin";

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
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
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }
    //--------------------------------------------------------------------------
    function ocd_answer ()
    {
        $id = $this->GetGP ("id");
        $message = $this->enc ($this->GetGP ("message"));
        $email = $this->db->GetOne ("Select email From `pub_tickets` Where pub_ticket_id='$id'");
        
        $first_name = $this->dec ($this->db->GetOne ("Select first_name From `pub_tickets` Where pub_ticket_id='$id'"));
        $last_name = $this->dec ($this->db->GetOne ("Select last_name From `pub_tickets` Where pub_ticket_id='$id'"));
        
        $ticket_code = $this->db->GetOne ("Select ticket_code From `pub_tickets` Where pub_ticket_id='$id'");
        $subject = $this->dec ($this->db->GetOne ("Select subject From `pub_tickets` Where pub_ticket_id='$id'"));

        $this->db->ExecuteSql ("Insert into `pub_ticket_messages` (pub_ticket_id, support_message, message, date_post) values ('$id', 1, '$message', '".time()."')");
        $this->db->ExecuteSql ("Update `pub_ticket` Set last_update='".time()."' Where pub_ticket_id='$id'");
        
        //visitor notification
            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='8'", ""); 
            if ($row ["is_active"] == 1)
            {
                
                $SiteTitle = $this->db->GetSetting ("SiteTitle");
                $SiteUrl = $this->db->GetSetting ("SiteUrl");
                
                $e_subject = $this->dec ($row ["subject"]);       
                $e_message = $this->dec ($row ["message"]);
                $e_subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $e_subject);
                
                $TicketLink = $SiteUrl."ticket.php?ocd=view&id=".$id."&code=".$ticket_code;
                $TicketShortLink = $SiteUrl."ticket.php?ocd=check";
                $e_message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $e_message);
                $e_message = preg_replace ("/\[FirstName\]/", $first_name, $e_message);
                $e_message = preg_replace ("/\[LastName\]/", $last_name, $e_message);
                $e_message = preg_replace ("/\[Email\]/", $email, $e_message);
                $e_message = preg_replace ("/\[TicketSubject\]/", $subject, $e_message);
                $e_message = preg_replace ("/\[TicketLink\]/", $TicketLink, $e_message);
                $e_message = preg_replace ("/\[TicketShortLink\]/", $TicketShortLink, $e_message);
                $e_message = preg_replace ("/\[TicketCode\]/", $ticket_code, $e_message);
                
                sendMail ($email, $e_subject, $e_message, $this->emailHeader);
                
            }
        $this->Redirect ($this->pageUrl."?ocd=view&id=$id");
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(Case When is_active=1 Then 0 Else 1 End) Where pub_ticket_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id");
        $count = $this->db->GetOne ("Select Count(*) From {$this->object} Where pub_ticket_id='$id'");
        if ($count > 0)
        {
            $this->db->ExecuteSql ("Delete From {$this->object} Where pub_ticket_id='$id'");
            $this->db->ExecuteSql ("Delete From `pub_ticket_messages` Where pub_ticket_id='$id'");
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

$zPage = new ZPage ("pub_tickets");

$zPage->Render ();

?>
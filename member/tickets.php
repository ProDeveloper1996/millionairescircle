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
        $this->orderDefault = "ticket_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
		  $this->mainTemplate = "./templates/ticket.tpl";
        $this->pageTitle = $dict['TN_pageTitle'];
        $this->pageHeader = $dict['TN_pageTitle']; 
        $mes = "";
        $err = $this->GetGP ("err");
        if ($err == "insert")
        {
            $mes = "<span class='error'>{$dict['TN_mess1']}</span>";
        }

        $activeStatus = ($this->GetGP ("activ", -2) != -2) ? $this->GetGP ("activ", 1) : $this->GetStateValue ("activ", 1);
        $this->SaveStateValue ("activ", $activeStatus);
        if ($activeStatus == -1) $activeStatusIN = "";
        else $activeStatusIN = "And is_active='$activeStatus'";
        $filter = (strlen ($activeStatus) > 0) ? $activeStatusIN : "";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where member_id='{$this->member_id}' $filter");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='{$dict['TN_CreateTicket']}'><img src='./images/add.png' border='0'></a>",
            "ACTIVE_STATUS_FILTER" => $this->getActiveTicketSelectMember ($activeStatus),
            "HEAD_ID" => $this->Header_GetSortLink ("ticket_id", "ID"),
            "HEAD_NAME" => "Member",
            "HEAD_SUBJECT" => $this->Header_GetSortLink ("subject", $dict['TN_Subject']),
            "HEAD_DATA_CREATE" => $this->Header_GetSortLink ("date_create", $dict['TN_Datecreate']),
            "HEAD_LAST_UPDATE" => $this->Header_GetSortLink ("last_update", $dict['TN_Lastupdate']),
            "HEAD_LAST_REPLIER" => $dict['TN_Lastreplier'],
            "MAIN_MES" => $mes,
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Where member_id='{$this->member_id}' $filter Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ticket_id'];
                $member = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name, ' (ID: ".$row['member_id'].")') From members Where member_id='".$row['member_id']."'", "n/a");
                $subject = $row['subject'];
                $date_create = date ("d M Y", $row['date_create']);
                $last_update = date ("d M Y", $row['last_update']);
                $last_replier = ($row['last_replier'] == 0) ? "Admin" : $member ;

                $activeLink = "<a href='{$this->pageUrl}?ocd=activate&id=$id'><img src='./images/active".$row['is_active'].".png' width='24' border='0' alt='{$dict['TN_mess2']}' title='{$dict['TN_mess2']}' /></a>";
                $viewLink = "<a href='{$this->pageUrl}?ocd=view&id=$id'><img src='./images/edit.png' border='0' alt='{$dict['TN_Details']}' title='{$dict['TN_Details']}' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('{$dict['TN_Del']}');\"><img src='./images/trash.png' width='24' border='0' alt='{$dict['TN_Delete']}' title='{$dict['TN_Delete']}' /></a>";
                $bgcolor = ($bgcolor == "") ? "#f5f5f5" : "";
            
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
            $bgcolor = ($bgcolor == "") ? "#f5f5f5" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function getActiveTicketSelectMember ($value)
    {
        GLOBAL $dict;
		  if ($value == "") $value = 1;
        $toRet = "<select name='activ' onChange='this.form.submit();'>";
        if ($value == -1) $check = "selected"; else $check = "";
        $toRet .= "<option value='-1' $check>{$dict['TN_Alltickets']}</option>";
        if ($value == 0) $check = "selected"; else $check = "";
        $toRet .= "<option value='0' $check>{$dict['TN_Closedtickets']}</option>";
        if ($value == 1) $check = "selected"; else $check = "";
        $toRet .= "<option value='1' $check>{$dict['TN_Opentickets']}</option>";
        return $toRet."</select>";
    }


    //--------------------------------------------------------------------------
    function ocd_view ()
    {
        GLOBAL $dict;
		  $this->mainTemplate = "./templates/ticket_details.tpl";
        $this->pageTitle = $dict['TN_pageTitle1'];
        $this->pageHeader = $dict['TN_pageTitle1'];
        $this->javaScripts = $this->GetJavaScript ();
        $id = $this->GetID ("id");
        $mes = "";
        $err = $this->GetGP ("err");
        if ($err == "answer")
        {
            $mes = "<span class='error'>{$dict['TN_mess3']}</span>";
        }
        $last_update = '';
        $date_create = '';
        $status='';
        $form='';
        $result = $this->db->ExecuteSql ("Select * From {$this->object} Where ticket_id='$id' And member_id='{$this->member_id}'");
        if ($row = $this->db->FetchInArray ($result))
        {
            $last_update = date ("d M Y h:i A", $row['last_update']);
            $date_create = date ("d M Y h:i A", $row['date_create']);
            $status = ($row['is_active'] == 1) ? "<span class='message'><b>{$dict['TN_Open']}</b></span>" : "<span class='error'><b>{$dict['TN_Close']}</b></span>";
            if ($row['is_active'])
            {
/*
                $form = "<form name='form1' action='$this->pageUrl' method='POST'>
                         <table width='100%' border='0' cellspacing='0' cellpadding='5' align='center' class='w_padding'>
                         <tr>
                             <td class='w_padding' width='20%' valign='top' ><span class='question'>{$dict['TN_Message']} :</span></td>
                             <td class='w_padding'><textarea name='message' cols='60' rows='8'></textarea> &nbsp;<span class='error'><div id='error'></div></span></td>
                         </tr>
                         <tr>
                             <td class='w_padding'>&nbsp;</td>
                             <td class='w_padding'>
                                <input type='submit' class='some_btn' value=' {$dict['TN_Reply']} ' onClick=\"return func ();\"> &nbsp;
                                <input type='button' class='some_btn' value=' {$dict['TN_Cancel']} ' onClick=\"window.location.href='$this->pageUrl'\">
                             </td>
                         </tr>
                         </table>
                         <input type='hidden' name='ocd' value='answer'>
                         <input type='hidden' name='id' value='$id'>
                         </form>";
*/
                         $form = '<form action="'.$this->pageUrl.'" method="POST">
<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <span class="error" id="error"></span>
            <label class="col-sm-4 control-label">'.$dict['TN_Message'].':</label>
            <div class="col-sm-8">
                <textarea name="message" cols="60" rows="8"></textarea>
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> '.$dict['TN_Reply'].'</button>
                    <button type="button" class="btn btn-form-cancel" onClick="window.location.href=\''.$this->pageUrl.'\'">'.$dict['TN_Cancel'].'</button>
            </div>
        </div>    
    </div>                              

</div>

    <input type="hidden" name="ocd" value="answer" />
    <input type="hidden" name="id" value="'.$id.'">
</form>';

            }
            else
            {
/*
                $form = "<form name='form1' action='$this->pageUrl' method='POST'>
                         <table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
                         <tr>
                             <td align='center'>
                                <input type='button' value=' {$dict['TN_Cancel']} ' onClick=\"window.location.href='$this->pageUrl'\">
                             </td>
                         </tr>
                         </table>
                         </form>";
*/
                         $form = '<form action="'.$this->pageUrl.'" method="POST">
<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="button" class="btn btn-form-cancel" onClick="window.location.href=\''.$this->pageUrl.'\'">'.$dict['TN_Cancel'].'</button>
            </div>
        </div>    
    </div>                              
</div>
</form>';

            }
        }
        $this->db->FreeSqlResult ($result);

        $total = $this->db->GetOne ("Select Count(*) From `ticket_messages` Where ticket_id='$id'");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_LAST_UPDATE" => $last_update,
            "MAIN_DATE_CREATE" => $date_create,
            "MAIN_STATUS" => $status,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_MES" => $mes,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_FORM" => $form,
            "MAIN_SUBJECT" => $row['subject'],
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "#607083";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `ticket_messages` Where ticket_id='$id' Order By date_post");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['ticket_message_id'];
                $message = nl2br ($this->dec ($row['message']));
                $date_post = date ("d M Y h:i A", $row['date_post']);
                $message_from = ($row['message_from'] == 0) ? "Admin" : $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From members Where member_id='".$row['message_from']."'", "n/a");
                $bgcolor = ($bgcolor == "#004fab") ? "" : "#004fab";
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
            $bgcolor = ($bgcolor == "") ? "#004fab" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['TN_pageTitle2'];
        $this->pageHeader = $dict['TN_pageTitle2'];
        $this->mainTemplate = "./templates/new_ticket.tpl";
        $subject = "<input type='text' name='subject' value='' maxlength='200' style='width: 420px;'>";
        $message = "<textarea name='message' cols='50' rows='10'></textarea>";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
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
        GLOBAL $dict;
		  $this->pageTitle = $dict['TN_pageTitle2'];
        $this->pageHeader = $dict['TN_pageTitle2'];
        $subject = $this->enc ($this->GetValidGP ("subject", $dict['TN_Subject'], VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", $dict['TN_Message'], VALIDATE_NOT_EMPTY));
        if ($this->errors['err_count'] > 0)
        {
            $this->pageTitle = $dict['TN_pageTitle2'];
            $this->pageHeader = $dict['TN_pageTitle2'];
            $this->mainTemplate = "./templates/new_ticket.tpl";
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_SUBJECT" => "<input type='text' name='subject' value='".$this->GetGP ("subject")."' maxlength='200' style='width: 420px;'>",
                "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
                "MAIN_MESSAGE" => "<textarea name='message' cols='50' rows='10'>".$this->GetGP ("message")."</textarea>",
                "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
                "MAIN_CANCEL_URL" => $this->pageUrl,
                "MAIN_OCD" => "insert",
            );
        }
        elseif ($this->member_id > 0)
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (member_id, subject, date_create, last_update, last_replier, is_read) values ('{$this->member_id}', '$subject', '".time()."', '".time()."', '{$this->member_id}', '0')");
            $ticket_id = $this->db->GetInsertID ();
            $this->db->ExecuteSql ("Insert into `ticket_messages` (ticket_id, message_from, message_to, message, date_post) values ('$ticket_id', '{$this->member_id}', '0', '$message', '".time()."')");

            $member = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name, ' (ID: {$this->member_id})') From members Where member_id='{$this->member_id}'", "n/a");
            $siteTitle = $this->db->GetOne ("Select value From `settings` Where keyname='SiteTitle'");
            $ticket_email = $this->db->GetOne ("Select value From `settings` Where keyname='ContactEmail'");
            $email_headers = "From: $siteTitle <$ticket_email>\r\n";
            $email_subject = "$siteTitle : New ticket";
                $email_body = "Dear Admin,\r\n".
                "Member $member, has created new ticket with subject \"$subject\". Ticket ID is $ticket_id\r\n".
                "\r\n\r\n---------------------\r\n$siteTitle";
            @mail ($ticket_email, $email_subject, $email_body, $email_headers);

            $this->Redirect ($this->pageUrl);
        }
        else {
            $this->Redirect ($this->pageUrl."?err=insert");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_answer ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['TN_pageTitle3'];
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>{$dict['TN_pageTitle2']}</a> / {$dict['TN_pageTitle3']}";
        $id = $this->GetID ("id");
        $message = $this->enc ($this->GetValidGP ("message", $dict['TN_Message'], VALIDATE_NOT_EMPTY));
        if ($this->member_id > 0)
        {
            $this->db->ExecuteSql ("Insert into `ticket_messages` (ticket_id, message_from, message_to, message, date_post) values ('$id', '{$this->member_id}', '0', '$message', '".time()."')");
            $this->db->ExecuteSql ("Update {$this->object} Set last_update='".time()."', last_replier='{$this->member_id}', is_read='0' Where ticket_id='$id'");

            $member = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name, ' (ID: {$this->member_id})') From members Where member_id='{$this->member_id}'", "n/a");
            $siteTitle = $this->db->GetOne ("Select value From `settings` Where keyname='SiteTitle'");
            $ticket_email = $this->db->GetOne ("Select value From `settings` Where keyname='ContactEmail'");
            $email_headers = "From: $siteTitle <$ticket_email>\r\n";
            $email_subject = "$siteTitle : New reply";
            $email_body = "\r\n Dear Admin,\r\n".
                "\r\n Member $member, add new reply to existing ticket. Ticket ID is $id \r\n".
                "\r\n\r\n---------------------\r\n$siteTitle";
            @mail ($ticket_email, $email_subject, $email_body, $email_headers);
        
            $this->Redirect ($this->pageUrl."?ocd=view&id=$id");
        }
        else
        {
            $this->Redirect ($this->pageUrl."?ocd=view&id=$id&err=answer");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where ticket_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id");
        $count = $this->db->GetOne ("Select Count(*) From {$this->object} Where ticket_id='$id' And member_id='{$this->member_id}'");
        if ($count > 0)
        {
            $this->db->ExecuteSql ("Delete From {$this->object} Where ticket_id='$id' And member_id='{$this->member_id}'");
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

$zPage = new ZPage ("`tickets`");

$zPage->Render ();

?>
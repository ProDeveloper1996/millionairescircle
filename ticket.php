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
        $this->orderDefault = "ticket_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
		  GLOBAL $dict;
        $this->mainTemplate = "./templates/ticket_new.tpl";
        $this->pageTitle = $dict['TN_pageTitle'];
        $this->pageHeader = $dict['TN_pageTitle'];

        $confirm = "";

        $code = $this->GetGP ("ec", "");

        if ($code != "") $confirm = "<span class='message'>{$dict['TN_Text2']}$code{$dict['TN_Text3']}</span>";

        $first_name = "";//<input type='text' name='first_name' value='' maxlength='50' style='width: 280px;'>";
        $last_name = "";//<input type='text' name='last_name' value='' maxlength='50' style='width: 280px;'>";
        $email = "";//<input type='text' name='email' value='' maxlength='120' style='width: 280px;'>";
        $subject = "";//<input type='text' name='subject' value='' maxlength='150' style='width: 280px;'>";
        $message = "";//<textarea name='message' rows='10' style='width: 490px;'></textarea>";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONFIRM" => $confirm,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FIRST_NAME" => $first_name,
            "MAIN_LAST_NAME" => $last_name,
            "MAIN_EMAIL" => $email,
            "MAIN_SUBJECT" => $subject,
            "MAIN_MESSAGE" => $message,
            "MAIN_OCD" => "insert",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
		  GLOBAL $dict;
        $first_name = $this->enc ($this->GetValidGP ("first_name", "First Name", VALIDATE_NOT_EMPTY));
        $last_name = $this->enc ($this->GetValidGP ("last_name", "Last Name", VALIDATE_NOT_EMPTY));
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));
        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0)
        {
            $this->pageTitle = $dict['TN_pageTitle'];
            $this->pageHeader = $dict['TN_pageTitle'];
            $this->mainTemplate = "./templates/ticket_new.tpl";

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_FIRST_NAME" => $first_name,//"<input type='text' name='first_name' value='".$first_name."' maxlength='50' style='width: 280px;'>",
                "MAIN_FIRST_NAME_ERROR" => $this->GetError ("first_name"),
                "MAIN_LAST_NAME" => $last_name,//"<input type='text' name='last_name' value='".$last_name."' maxlength='50' style='width: 280px;'>",
                "MAIN_LAST_NAME_ERROR" => $this->GetError ("last_name"),
                "MAIN_EMAIL" => $email,//"<input type='text' name='email' value='".$email."' maxlength='120' style='width: 280px;'>",
                "MAIN_EMAIL_ERROR" => $this->GetError ("email"),
                "MAIN_SUBJECT" => $subject,//"<input type='text' name='subject' value='".$subject."' maxlength='150' style='width: 280px;'>",
                "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
                "MAIN_MESSAGE" => $message,//"<textarea name='message' rows='10' style='width: 490px;'>".$message."</textarea>",
                "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
                "MAIN_OCD" => "insert",
            );
        }
        else
        {
            // Save ticket in database
            $ticket_code = getUnID (8);
            $this->db->ExecuteSql ("Insert into {$this->object} (first_name, last_name, email, ticket_code, subject, date_create, last_update, is_active) values ('$first_name', '$last_name', '$email', '$ticket_code', '$subject', '".time()."', '".time()."', '1')");
            $ticket_id = $this->db->GetInsertID ();
            $this->db->ExecuteSql ("Insert into `pub_ticket_messages` (pub_ticket_id, support_message, message, date_post) values ('$ticket_id', '0', '$message', '".time()."')");

            $SiteTitle = $this->db->GetSetting ("SiteTitle");
            $SiteUrl = $this->db->GetSetting ("SiteUrl");
            
            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='7'", ""); 
            if ($row ["is_active"] == 1)
            {
                $e_subject = $this->dec ($row ["subject"]);
                $e_message = $this->dec ($row ["message"]);
                $e_subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $e_subject);
                
                $TicketLink = $SiteUrl."ticket.php?ocd=view&id=".$ticket_id."&code=".$ticket_code;
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
            
            // Send email to admin about new public ticket
            $ticket_email = $this->db->GetSetting ("ContactEmail");
            $email_subject = "$SiteTitle : New public ticket";
            $email_body = "Dear Admin,\r\n".
                "New public ticket was registered. Here's details: \r\n\r\n".
                "Ticket ID: $ticket_id \r\n".
                "First name: $first_name \r\n".
                "Last name: $last_name \r\n".
                "E-mail: $email \r\n".
                "Subject: $subject \r\n".
                "Message: \r\n ".$this->GetGP ("message")." \r\n\r\n".
                "Please admin panel to answer it. ".
                "\r\n\r\n---------------------\r\n$SiteTitle";
            sendMail ($ticket_email, $email_subject, $email_body, $this->emailHeader);

            $this->Redirect ($this->pageUrl."?ec=$ticket_code");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_check ()
    {
		  GLOBAL $dict;
        $this->mainTemplate = "./templates/ticket_login.tpl";
        $this->pageTitle = "Check a Ticket";
        $this->pageHeader = "Check a Ticket";

        $confirm = "";
        if ($this->GetGP ("ec") == "error") $confirm = "<span class='error'>{$dict['TN_Text4']}</span>";

        $email = "<input type='text' name='email' value='' maxlength='120' style='width: 280px; ' class='form-control' placeholder='Your e-mail address'>";
        $ticket_code = "<input type='text' name='ticket_code' value='' maxlength='20' style='width: 120px;  ' class='form-control' placeholder='Ticket code' >";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONFIRM" => $confirm,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_EMAIL" => $email,
            "MAIN_CODE" => $ticket_code,
            "MAIN_OCD" => "login",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_login ()
    {
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));
        $ticket_code = $this->enc ($this->GetGP ("ticket_code"));
        if ($this->errors['err_count'] > 0)
        {
            $this->Redirect ($this->pageUrl."?ocd=check&ec=error");    
        }
        $ticket_id = $this->db->GetOne ("Select pub_ticket_id From {$this->object} Where email='$email' And ticket_code='$ticket_code'", 0);
        if ($ticket_id > 0) {
            $this->Redirect ($this->pageUrl."?ocd=view&id=$ticket_id&code=$ticket_code");
        }
        else {
            $this->Redirect ($this->pageUrl."?ocd=check&ec=error");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_view ()
    {
		  GLOBAL $dict;
        $this->mainTemplate = "./templates/ticket_details.tpl";
        $this->pageTitle = $dict['TN_pageTitleDet'];
        $this->pageHeader = $dict['TN_pageTitleDet'];
        $this->javaScripts = $this->GetJavaScript ();

        $code_t = $this->GetGP ("code_t", "");
//        print $code_t;
        if ($code_t == "")
        {
            $id = $this->GetID ("id", 0);
            $ticket_code = $this->GetGP ("code");
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where pub_ticket_id='$id' And ticket_code='$ticket_code'");
        }
        else
        {
            $ticket_code = $code_t;
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where ticket_code='$code_t'");
            $id = $this->db->GetOne ("Select pub_ticket_id From `{$this->object}` Where ticket_code='$code_t'");
        }

        if ($row = $this->db->FetchInArray ($result))
        {
            $this->db->FreeSqlResult ($result);
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
            $subject = $row['subject'];
            $last_update = date ("d M Y h:i A", $row['last_update']);
            $date_create = date ("d M Y h:i A", $row['date_create']);
            $status = ($row['is_active'] == 1) ? "<span class='message'><b>{$dict['TN_Open']}</b></span>" : "<span class='error'><b>{$dict['TN_Closed']}</b></span>";

            $form = "";
            if ($row['is_active'])
            {
                $form = "<form name='form1' action='{$this->pageUrl}' method='POST'  style='padding:0px;margin:0px;'>


                        <table width='100%' cellpadding='2' cellspacing='0' border='0' align='center'>
                        <tr>
                            <td class='w_padding' valign='top' align='center'>
                                <span class='question'>{$dict['TN_Message']} :</span>
                            </td>
                        </tr>
                        <tr>
                            <td class='w_padding' align='center'>
                                <textarea class='form-control' name='message' cols='60' rows='4'></textarea> &nbsp;<span class='error'><div id='error'></div></span>
                            </td>
                        </tr>
                        <tr>
                            <td class='w_padding' align='center'>
                                <button style='width: 200px;' type='submit' class='btn btn-form-login' onClick=\"return validate ();\" > {$dict['TN_Reply']}</button>
                            </td>
                        </tr>
                        </table>
                                <input type='hidden' name='ocd' value='reply'>
                                <input type='hidden' name='id' value='$id'>
                                <input type='hidden' name='code' value='$ticket_code'>
                                </form>";


            }

            $total = $this->db->GetOne ("Select Count(*) From `pub_ticket_messages` Where pub_ticket_id='$id'");
            $this->data = array (
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_TICKET_ID" => $id,
                "MAIN_DATE_CREATE" => $date_create,
                "MAIN_LAST_UPDATE" => $last_update,
                "MAIN_STATUS" => $status,
                "MAIN_SUBJECT" => $subject,
                "MAIN_FORM" => $form,
                "MAIN_SELECT" => $this->select_ticket ($email, $ticket_code),
                "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
            );

            $bgcolor = "#fff";
            if ($total > 0)
            {
                $result2 = $this->db->ExecuteSql ("Select * From `pub_ticket_messages` Where pub_ticket_id='$id' Order By date_post");
                while ($row2 = $this->db->FetchInArray ($result2))
                {
                    $id = $row2['pub_ticket_message_id'];
    
                    $message = nl2br ($this->dec ($row2['message']));
                    $date_post = date ("d M Y h:i A", $row2['date_post']);
                    $message_from = ($row2['support_message'] == 1) ? "{$dict['TN_SupportTeam']}" : "$first_name $last_name";
                
                    //$bgcolor = ($bgcolor == "#76889d") ? "#65768a" : "#76889d";
                    $bgcolor = ($bgcolor == "#fff") ? "#FAFAFA" : "#fff";
                
                    $this->data ['TABLE_ROW'][] = array (
                        "ROW_FROM" => $message_from,
                        "ROW_DATE_POST" => $date_post,
                        "ROW_MESSAGE" => $message,
                        "ROW_BGCOLOR" => $bgcolor
                    );
                }
                $this->db->FreeSqlResult ($result2);
            }
            else
            {
                $bgcolor = '';//($bgcolor == "#76889d") ? "#65768a" : "#76889d";
                $this->data ['TABLE_EMPTY'][] = array (
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
        }
        else {
            $this->Redirect ($this->pageUrl."?ocd=check");
        }
    }

    //--------------------------------------------------------------------------
    function select_ticket ($value = "", $ticket_code)
    {
        $total = $this->db->GetOne ("Select Count(*) From `pub_tickets` Where email='$value'", 0);
        if ($total > 0)
        {
            $toRet = "<select class='form-control' name='code_t' style='width:240px;' onChange='this.form.submit();'> \r\n";
            $result = $this->db->ExecuteSql ("Select ticket_code, subject From `pub_tickets` Where email='$value' Order By pub_ticket_id");
            while ($row = $this->db->FetchInArray ($result))
            {
                $selected = ($row['ticket_code'] == $ticket_code) ? "selected" : "";
                $toRet .= "<option value='".$row['ticket_code']."' $selected>".$row['subject']."</option>";
            }
            $this->db->FreeSqlResult ($result);
            return $toRet."</select>\r\n";
        }

    }
    //--------------------------------------------------------------------------
    function ocd_reply ()
    {
        $id = $this->GetID ("id");
        $ticket_code = $this->enc ($this->GetGP ("code"));
        $message = $this->enc ($this->GetGP ("message"));

        $count = $this->db->GetOne ("Select Count(*) From {$this->object} Where pub_ticket_id='$id' And ticket_code='$ticket_code'");
        if ($count > 0)
        {
            $the_time = time ();
            $this->db->ExecuteSql ("Insert into `pub_ticket_messages` (pub_ticket_id, support_message, message, date_post) values ('$id', '0', '$message', '$the_time')");
            $this->db->ExecuteSql ("Update {$this->object} Set last_update='$the_time', is_read='0' Where pub_ticket_id='$id'");

            $siteTitle = $this->db->GetSetting ("SiteTitle");
            $ticket_email = $this->db->GetSetting ("ContactEmail");

            $email_headers = "From: $siteTitle <$ticket_email>\r\n";
            $email_subject = "$siteTitle : New reply on existing public ticket";
            $email_body = "\r\n Dear Admin, \r\n\r\n".
                "Creator of public ticket #$id just sent new message to this ticket. \r\n".
                "\r\n\r\n---------------------\r\n$siteTitle";
            @mail ($ticket_email, $email_subject, $email_body, $this->emailHeader);
        
            $this->Redirect ($this->pageUrl."?ocd=view&id=$id&code=$ticket_code");
        }
        else {
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script type="text/javascript" language="JavaScript">
        <!--
            function validate ()
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
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
        $this->orderDefault = "member_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $ec = $this->GetGP ("ec");
        $this->mainTemplate = "./templates/mailing.tpl";
        $this->pageTitle = "Send Mass Mail";
        $this->pageHeader = "Send Mass Mail";
        $filter = $this->GetGP ("filter");
        if ($filter == 1)
        {
            $filterDateDayBegin = $this->GetGP ("filterDateDayBegin");
            $filterDateMonthBegin = $this->GetGP ("filterDateMonthBegin");
            $filterDateYearBegin = $this->GetGP ("filterDateYearBegin");
            $filterDateDayEnd = $this->GetGP ("filterDateDayEnd");
            $filterDateMonthEnd = $this->GetGP ("filterDateMonthEnd");
            $filterDateYearEnd = $this->GetGP ("filterDateYearEnd");

            $this->SaveStateValue ("filterDateBegin", mktime(0, 0, 0, $filterDateMonthBegin, $filterDateDayBegin, $filterDateYearBegin));
            $this->SaveStateValue ("filterDateEnd", mktime(23, 59, 59, $filterDateMonthEnd, $filterDateDayEnd, $filterDateYearEnd));
            $this->SaveStateValue ("member_id", $this->enc ($this->GetGP ("member_id")));
            $this->SaveStateValue ("sponsor_id", $this->enc ($this->GetGP ("sponsor_id")));
            $this->SaveStateValue ("last_name", $this->enc ($this->GetGP ("last_name")));
            $this->SaveStateValue ("email", $this->enc ($this->GetGP ("email")));
            $this->SaveStateValue ("active", $this->enc ($this->GetGP ("active")));
        }
        $sql_select = "";
        $sel_inact = "";
        $sel_act = "";
        $sel_all = "";
        $filterDateBegin = $this->GetStateValue ("filterDateBegin", 0);
        $filterDateEnd = $this->GetStateValue ("filterDateEnd", 0);
        $member_id = $this->GetStateValue ("member_id");
        $sponsor_id = $this->GetStateValue ("sponsor_id");
        $last_name = $this->GetStateValue ("last_name");
        $email = $this->GetStateValue ("email");
        $active = $this->GetStateValue ("active");

        $filterDateDayBegin = ($filterDateBegin != 0) ? date ("d", $filterDateBegin) : "";
        $filterDateMonthBegin = ($filterDateBegin != 0) ? date ("m", $filterDateBegin) : "";
        $filterDateYearBegin = ($filterDateBegin != 0) ? date ("Y", $filterDateBegin) : date ("Y", time())-1;
        $filterDateDayEnd = ($filterDateEnd != 0) ? date ("d", $filterDateEnd) : "";
        $filterDateMonthEnd = ($filterDateEnd != 0) ? date ("m", $filterDateEnd) : "";
        $filterDateYearEnd = ($filterDateEnd != 0) ? date ("Y", $filterDateEnd) : "";

        if ($member_id != "") $sql_select .= " And member_id='$member_id'";
        if ($sponsor_id != "") $sql_select .= " And enroller_id='$sponsor_id'";
        if ($last_name != "") $sql_select .= " And last_name='$last_name'";
        if ($email != "") $sql_select .= " And email='$email'";
        if ($filterDateBegin != 0) $sql_select .= " And reg_date>$filterDateBegin And reg_date<$filterDateEnd";

        switch ($active)
        {
            case "active":
                 $sql_select .= " And is_active='1'";
                 $sel_act = "selected";
            break;
            case "inactive":
                 $sql_select .= " And is_active='0'";
                 $sel_inact = "selected";
            break;
            default:
                $sel_all = "selected";
        }
        $filter = "";
        $filter .= "Date from ";
        $filter .= getDaySelect ($filterDateDayBegin, "filterDateDayBegin");
        $filter .= getMonthSelect ($filterDateMonthBegin, "filterDateMonthBegin");
        $filter .= getYearSelect ($filterDateYearBegin, "filterDateYearBegin", $this->object, "reg_date");
        $filter .= " to ";
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd", $this->object, "reg_date");
        $select_active = "<select name='active' style='width:120px;' maxlength='6'><option value='all' $sel_all>All
                        <option value='active' $sel_act>Active
                        <option value='inactive' $sel_inact>Inactive
                        </select>";
        $total_selected = $this->db->GetOne ("Select Count(*) From selected");
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,
            "AMOUNT_SEL" => $total_selected,
            "MEMBER_ID" => "<input type='text' name='member_id' value='$member_id' style='width:60px;' maxlength='6'>",
            "SPONSOR_ID" => "<input type='text' name='sponsor_id' value='$sponsor_id' style='width:60px;' maxlength='6'>",
            "LAST_NAME" => "<input type='text' name='last_name' value='$last_name' style='width:200px;' maxlength='50'>",
            "EMAIL" => "<input type='text' name='email' value='$email' style='width:200px;' maxlength='50'>",
            "ACTIVE" => $select_active,
            "HEAD_MEMBER_ID" => $this->Header_GetSortLink ("member_id", "ID"),
            "HEAD_USERNAME" => $this->Header_GetSortLink ("username", "Username"),
            "HEAD_FIRST_NAME" => $this->Header_GetSortLink ("first_name", "First name"),
            "HEAD_LAST_NAME" => $this->Header_GetSortLink ("last_name", "Last name"),
            "HEAD_REG_DATE" => $this->Header_GetSortLink ("reg_date", "Registration date"),
            "HEAD_EMAIL" => $this->Header_GetSortLink ("email", "E-mail"),
            "HEAD_SPONSOR" => $this->Header_GetSortLink ("enroller_id", "Sponsor's ID"),
            "HEAD_CHECKBOX" => "<input type='checkbox' name='sel_all' onClick='select_all (this.form);'>",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Where 1 $sql_select Order By {$this->orderBy} {$this->orderDir}", true);
            $members = array();
            while ($row = $this->db->FetchInArray ($result))
            {
                $member_id = $row['member_id'];
                $username = $this->dec ($row['username']);
                $firstname = $this->dec ($row['first_name']);
                $lastname = $this->dec ($row['last_name']);
                $email = $this->dec ($row['email']);
                $sponsor_id = $row['enroller_id'];
                $reg_date = date ("d-m-Y", $row['reg_date']);
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_MEMBER_ID" => $member_id,
                    "ROW_USERNAME" => $username,
                    "ROW_FIRST_NAME" => $firstname,
                    "ROW_LAST_NAME" => $lastname,
                    "ROW_EMAIL" => $email,
                    "ROW_REG_DATE" => $reg_date,
                    "ROW_SPONSOR" => $sponsor_id,
                    "ROW_BGCOLOR" => $bgcolor,
                    "ROW_CHECKBOX" => "<input type='checkbox' name='members[]' value='".$member_id."'>"
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
    function ocd_moveto ()
    {
        $rt = $this->GetGPArray ('members');
        
        if ($rt != "")
        {
            foreach ($rt as $each)
            {
                $sel = $this->db->GetOne ("Select Count(*) From selected Where member_id='$each'");
                if ($sel == 0) $this->db->ExecuteSql ("Insert into selected (member_id) values ('$each')");
            }
        }
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_moveall ()
    {
        $result = $this->db->ExecuteSql ("Select * From members");
        while ($row = $this->db->FetchInArray ($result))
        {
            $member_id = $row['member_id'];
            $sel = $this->db->GetOne ("Select Count(*) From `selected` Where member_id='$member_id'");
            if ($sel == 0) $this->db->ExecuteSql ("Insert into `selected` (member_id) values ('$member_id')");
        }
        $this->db->FreeSqlResult ($result);
        $this->Redirect ($this->pageUrl);
    }
    
    //--------------------------------------------------------------------------
    function ocd_moveinact ()
    {
        $result = $this->db->ExecuteSql ("Select `member_id` From `members` Where `is_active`=0 And `is_dead`=0");
        while ($row = $this->db->FetchInArray ($result))
        {
            $member_id = $row['member_id'];
            $sel = $this->db->GetOne ("Select Count(*) From `selected` Where member_id='$member_id'");
            if ($sel == 0) $this->db->ExecuteSql ("Insert into `selected` (member_id) values ('$member_id')");
        }
        $this->db->FreeSqlResult ($result);
        $this->Redirect ($this->pageUrl);
    }
    
    //--------------------------------------------------------------------------
    function ocd_movefree ()
    {
        $cycling = $this->db->GetSetting ("cycling", 0);
        $sql = ($cycling == 1)? " `m_level`=0 " : " `m_level`<2 ";
        $result = $this->db->ExecuteSql ("Select `member_id` From `members` Where `is_active`=1 And `is_dead`=0 And $sql");
        while ($row = $this->db->FetchInArray ($result))
        {
            $member_id = $row['member_id'];
            $sel = $this->db->GetOne ("Select Count(*) From `selected` Where member_id='$member_id'");
            if ($sel == 0) $this->db->ExecuteSql ("Insert into `selected` (member_id) values ('$member_id')");
        }
        $this->db->FreeSqlResult ($result);
        $this->Redirect ($this->pageUrl);
    }

//--------------------------------------------------------------------------
    function ocd_movepaid ()
    {
        $cycling = $this->db->GetSetting ("cycling", 0);
        $sql = ($cycling == 1)? " `m_level`>0 " : " `m_level`>1 ";
        $result = $this->db->ExecuteSql ("Select `member_id` From `members` Where `is_active`=1 And `is_dead`=0 And $sql");
        while ($row = $this->db->FetchInArray ($result))
        {
            $member_id = $row['member_id'];
            $sel = $this->db->GetOne ("Select Count(*) From `selected` Where member_id='$member_id'");
            if ($sel == 0) $this->db->ExecuteSql ("Insert into `selected` (member_id) values ('$member_id')");
        }
        $this->db->FreeSqlResult ($result);
        $this->Redirect ($this->pageUrl);
    }
  
    //--------------------------------------------------------------------------
    function ocd_sellist ()
    {
        $this->pageTitle = "Mailing Members";
        $this->mainTemplate = "./templates/mailing_list.tpl";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Mailing Members</a> / Selected Members";
        $this->javaScripts = $this->GetJavaScript ();
        $total_selected = $this->db->GetOne ("Select Count(*) From selected");
        $m = $this->GetGP ("m");
        $thanks = ($m == "ok")? "Emails were successfully sent" : "";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "AMOUNT_SEL" => $total_selected,
            "HEAD_MEMBER_ID" => $this->Header_GetSortLinkS ("member_id", "Member's ID"),
            "HEAD_USERNAME" => $this->Header_GetSortLinkS ("username", "Username"),
            "HEAD_FIRST_NAME" => $this->Header_GetSortLinkS ("first_name", "First name"),
            "HEAD_LAST_NAME" => $this->Header_GetSortLinkS ("last_name", "Last name"),
            "HEAD_REG_DATE" => $this->Header_GetSortLinkS ("reg_date", "Registration date"),
            "HEAD_EMAIL" => $this->Header_GetSortLinkS ("email", "E-mail"),
            "HEAD_SPONSOR" => $this->Header_GetSortLinkS ("enroller_id", "Sponsor's ID"),
            "HEAD_CHECKBOX" => "<input type='checkbox' name='sel_all' onClick='select_all (this.form);'>",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total_selected, $this->pageUrl."?ocd=sellist&"),
            "MAIN_CONFIRM" => $thanks,
        );
        $bgcolor = "";
        if ($total_selected > 0)
        {
            $this->data["SEND_EMAIL"] = "<input class='some_btn' type='submit' value='Send Email to the members in the list'>";
            $result = $this->db->ExecuteSql ("Select * From members, selected Where members.member_id=selected.member_id Order By members.{$this->orderBy} {$this->orderDir}", true);
            $members = array();
            while ($row = $this->db->FetchInArray ($result))
            {
                $member_id = $row['member_id'];
                $username = $this->dec ($row['username']);
                $firstname = $this->dec ($row['first_name']);
                $lastname = $this->dec ($row['last_name']);
                $email = $this->dec ($row['email']);
                $sponsor_id = $row['enroller_id'];
                $reg_date = date ("d-m-Y", $row['reg_date']);
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_MEMBER_ID" => $member_id,
                    "ROW_USERNAME" => $username,
                    "ROW_FIRST_NAME" => $firstname,
                    "ROW_LAST_NAME" => $lastname,
                    "ROW_EMAIL" => $email,
                    "ROW_REG_DATE" => $reg_date,
                    "ROW_SPONSOR" => $sponsor_id,
                    "ROW_BGCOLOR" => $bgcolor,
                    "ROW_CHECKBOX" => "<input type='checkbox' name='members[]' value='".$member_id."'>"
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
    function ocd_clear ()
    {
        $this->db->ExecuteSql("TRUNCATE TABLE `selected`");
        $this->Redirect ($this->pageUrl."?ocd=sellist");
    }

    //--------------------------------------------------------------------------
    function ocd_delsome ()
    {
        $rt = $this->GetGPArray ('members');
        
        if ($rt != "")
        {
            foreach ($rt as $each)
            {
                $this->db->ExecuteSql ("Delete From selected Where member_id='$each'");
            }
        }
        $this->Redirect ($this->pageUrl."?ocd=sellist");
    }

    //--------------------------------------------------------------------------
    function ocd_mail ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/mailing_details.tpl";
        $this->pageTitle = "Mailing Members";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Mailing Members</a>/ <a href='{$this->pageUrl}?ocd=sellist'>Selected Members</a> / Send Email";
        $total = $this->db->GetOne ("Select Count(*) From selected");
        
        $emailtempl_id = $this->GetGP ("emailtempl_id", 1);
        if ($emailtempl_id > 0) $this->SaveStateValue ("emailtempl_id", $emailtempl_id);
        $emailtempl_id = $this->GetStateValue ("emailtempl_id", 0);
        
        $subject = $this->db->GetOne ("Select subject From aemailtempl Where emailtempl_id='$emailtempl_id'");
        $subject = $this->dec ($subject);
        $message = $this->db->GetOne ("Select message From aemailtempl Where emailtempl_id='$emailtempl_id'");
        $message = $this->dec ($message);
        $ch_templ = $this->db->GetOne("Select tag_descr From aemailtempl Where emailtempl_id=1");
        $e_subject = "<input type='text' name='subject' value='$subject' maxlength='250' style='width:340px;'>";
        //$e_message = "<textarea name='message' rows='15' style='width:440px;'>$message</textarea>";
        $e_message = $this->FCKeditor( "message", htmlspecialchars_decode($message) );
        $perm = "";
        if ($emailtempl_id == 1) $perm = "disabled";
        $btn ="<input type=radio name=check value=1 checked> Send email without updating template<br><br><input type=radio name=check value=2> Send email and update template <br><br><input type=radio name=check value=3> Send email and save template as a new <br><br><input type=radio name=check value=4> Update template without sending email<br><br><input type=radio name=check value=5 $perm> Delete template";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "TOTAL_LIST" => $total,
            "EMAIL_SUBJECT" => $e_subject,
            "EMAIL_MESSAGE" => $e_message,
            "MAIN_FLAG" => $btn,
            "CHANGE_TEMPLATE" => $ch_templ,
            "MAIN_SELECT" => $this->getPageSelect2 ($emailtempl_id),
        );
    }

    //--------------------------------------------------------------------------
    function ocd_send_email ()
    {
        $subject = $this->enc ($this->GetGP ('subject'));
        $message_origin = $this->GetGP ('message');
        
        $message_origin_coded = $this->enc ($message_origin);
        $subject_coded = $this->enc ($subject);
        
        $emailtempl_id = $this->GetStateValue ("emailtempl_id", 0);
        $check = $this->GetGP ('check');
        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $siteEmail = $this->db->GetSetting ("ContactEmail");
        $siteUrl = $this->db->GetSetting ("SiteUrl");
        
        if ($check == 5)
        {
            $this->db->ExecuteSql ("Delete From `aemailtempl` Where emailtempl_id='$emailtempl_id' And emailtempl_id!=1");
            $this->Redirect ($this->pageUrl."?ocd=sellist&m=ok");
        }
        
        if ($check == 2 or $check == 4) $this->db->ExecuteSql ("Update aemailtempl Set subject='$subject_coded', message='$message_origin_coded' Where emailtempl_id='$emailtempl_id'");
        if ($check == 3) $this->db->ExecuteSql ("Insert into aemailtempl (subject, message) values ('$subject_coded', '$message_origin_coded')"); 

        $subject = preg_replace ("/\[SiteTitle\]/", $siteTitle, $subject);
        $count = $this->db->GetOne ("Select Count(*) From `members`, `selected` Where members.member_id=selected.member_id", 0);
        
        $useFoneMailing = $this->db->GetSetting ("useFoneMailing", 0);
        if ($useFoneMailing == 1)
        {
            
            if ($count > 0 And $check < 4)
            {
                $this->db->SetSetting ("subjectMail", $subject_coded);
                $this->db->SetSetting ("messageMail", $message_origin_coded);
            
                $retval = "";
                $phpPath = "php";
                $pathSite = $this->db->GetSetting ("PathSite");
                if (substr ($pathSite, -1) == "/") $pathSite = substr ($pathSite, 0, -1);

                session_write_close ();
                //system ($phpPath." ".$pathSite."/admin/mailing_sys.php ".$pathSite." > /dev/null 2>&1 &", $retval);
                session_start ();
            
            }

            $subject = "Mailing result";
            $message = "Mailing to $count members is successfully completed";
            sendMail ($siteEmail, $subject, $message, $this->emailHeader);
            $this->Redirect ($this->pageUrl."?ocd=sellist&m=ok");
        }
        else
        {
            if ($count > 0 And $check < 4)
            {
            
                $result = $this->db->ExecuteSql ("Select * From `members`, `selected` Where members.member_id=selected.member_id");
                while ($row = $this->db->FetchInArray ($result))
                {
                    $message = $message_origin;
                    $member_id = $row['member_id'];
                    $firstname = $this->dec ($row['first_name']);
                    $lastname = $this->dec ($row['last_name']);

                    $email = $row['email'];
                    $username = $row['username'];
                    $enroller_id = $row['enroller_id'];

                    $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
                    $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
                    $ref_link = $siteUrl."ref=".$ref_id;

                    $message = preg_replace ("/\[SiteTitle\]/", $siteTitle, $message);
                    $message = preg_replace ("/\[SiteUrl\]/", $siteUrl, $message);
                    $message = preg_replace ("/\[FirstName\]/", $firstname, $message);
                    $message = preg_replace ("/\[LastName\]/", $lastname, $message);
                    $message = preg_replace ("/\[ID\]/", $member_id, $message);
                    $message = preg_replace ("/\[Username\]/", $username, $message);
                    $message = preg_replace ("/\[Email\]/", $email, $message);
                    $message = preg_replace ("/\[RefLink\]/", $ref_link, $message);
                    $message = preg_replace ("/\[SponsorID\]/", $enroller_id, $message);
    
                    $message .= "\r\n\r\nDo not reply to this email as it will not be seen - to contact us use the ticket system on the website in the public area or members area.";

                    sendMail ($email, $subject, $message, $this->emailHeader);
                
                }
                $this->db->FreeSqlResult ($result);
            
            }

            $subject = "Mailing result";
            $message = "Mailing to $count members is successfully completed";
            sendMail ($siteEmail, $subject, $message, $this->emailHeader);
            $this->Redirect ($this->pageUrl."?ocd=sellist&m=ok");
        }
    }

    //--------------------------------------------------------------------------
    function getPageSelect2 ($value = 0)
    {
        $toRet = "<select name='emailtempl_id' style='width:242px;' onChange='this.form.submit();'> \r\n";
        $result = $this->db->ExecuteSql ("Select * From `aemailtempl` Order By emailtempl_id ");
        while ($row = $this->db->FetchInArray ($result))
        {
            $selected = ($row['emailtempl_id'] == $value) ? "selected" : "";
            $toRet .= "<option value='".$row['emailtempl_id']."' $selected>".$row['subject']."</option>";
        }
        return $toRet."</select>\r\n";
    }

//--------------------------------------------------------------------------
function GetJavaScript ()
{
    return <<<_ENDOFJS_
    <script type="text/javascript" language="JavaScript">
        <!--
        function insertText (text)
        {
            var taField = document.form1.message;
            //IE support
            if (document.selection)
            {
                taField.focus();
                sel = document.selection.createRange ();
                sel.text = text;
            }
        //MOZILLA/NETSCAPE support
            else if (taField.selectionStart || taField.selectionStart == '0')
            {
                var startPos = taField.selectionStart;
                var endPos = taField.selectionEnd;
                taField.value = taField.value.substring (0, startPos) + text + taField.value.substring (endPos, taField.value.length);
            } else
            {
                taField.value += text;
            }
        }

        function select_all (theForm)
            {
                var elements = theForm.elements['members[]'];
                if (elements.length > 0) {
                    elts_cnt = elements.length;
                    for (var i = 0; i < elts_cnt; i++) {
                        if (theForm.sel_all.checked == true) {
                            elements[i].checked = true;
                        }
                        else {
                            elements[i].checked = false;
                        }
                    }
                }
                else {
                    if (theForm.sel_all.checked == true) {
                        theForm.elements['members[]'].checked = true;
                    }
                    else {
                        theForm.elements['members[]'].checked = false;
                    }
                }
            }
        -->
    </script>
_ENDOFJS_;
}
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("members");

$zPage->Render ();

?>
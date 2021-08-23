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
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/atemplates.tpl";
        $this->pageTitle = "Email Templates for Mass Mailing";
        $this->pageHeader = "Email Templates for Mass Mailing";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_SUBJECT" => "Subject",
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='Add Template'><img src='./images/add.png'></a>",
            
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Order By emailtempl_id asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $emailtempl_id = $row['emailtempl_id'];
                $subject = $this->dec($row['subject']);
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"emailtempl_id\", ".$emailtempl_id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$emailtempl_id'><img src='./images/edit.png' width='25' border='0' alt='Edit'></a>";
                
                $delLink = ($emailtempl_id > 1)? "<a href='{$this->pageUrl}?ocd=del&id=$emailtempl_id' onClick=\"return confirm ('Do you really want to delete this template?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>" : "&nbsp;";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $emailtempl_id,
                    "ROW_SUBJECT" => $subject,
                    "ROW_ACTIVELINK" => $activeLink,
                    "ROW_EDITLINK" => $editLink,
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
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }
    
    
    //--------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/atemplate_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript2 ();
        
        $subst = $this->dec ($this->db->GetOne ("Select `tag_descr` From `{$this->object}` Where emailtempl_id='1'"));

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where emailtempl_id='$id'", $this->pageUrl);
                $subject = "<input type='text' name='subject' value='".$row["subject"]."' maxlength='250' style='width: 500px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'>".$row["message"]."</textarea>";
                

            break;

            case FORM_FROM_GP:

                $subject = "<input type='text' name='subject' value='".$this->GetGP ("subject")."' maxlength='250' style='width: 500px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'>".$this->GetGP ("message")."</textarea>";
                
            break;
            case FORM_EMPTY:
            default:

                $subject_s = $this->db->GetOne ("Select `subject` From `{$this->object}` Where emailtempl_id='1'");
                $message_s = $this->db->GetOne ("Select `message` From `{$this->object}` Where emailtempl_id='1'");
                
                $subject = "<input type='text' name='subject' value='".$subject_s."' maxlength='250' style='width: 500px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'>".$message_s."</textarea>";

            break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_SUBJECT" => $subject,
            "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
            
            "MAIN_MESSAGE" => $message,
            "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
            
            "SUBSTITUTIONS" => $subst,

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "New Template";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Email Templates for Mass Mailing</a> / New Template";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "New Template";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Email Templates for Mass Mailing</a> / New Template";
        
        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            
            $this->db->ExecuteSql ("Insert into {$this->object} (subject, message, is_active, `tag_descr`) values ('$subject', '$message', 1, '')");
            $this->Redirect ($this->pageUrl);
            
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Email Templates for Mass Mailing</a> / Edit Template";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Email Templates for Mass Mailing</a> / Edit Template";
        $id = $this->GetGP ("id");
        
        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {

            $this->db->ExecuteSql ("Update {$this->object} Set subject='$subject', message='$message' Where emailtempl_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }
    
    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Delete From {$this->object} Where emailtempl_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

//--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }
    
    //--------------------------------------------------------------------------
function GetJavaScript2 ()
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
        -->
    </script>
_ENDOFJS_;
}
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("aemailtempl");

$zPage->Render ();

?>
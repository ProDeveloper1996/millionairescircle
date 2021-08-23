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
        $this->mainTemplate = "./templates/autoresponders.tpl";
        $this->pageTitle = "Active Members Autoresponder Email Templates";
        $this->pageHeader = "Active Members Autoresponder Email Templates";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where `is_free`=0");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='To add a template'><img src='./images/add.png' border='0'></a>",
            "HEAD_SUBJECT" => "Subject",
            "HEAD_DAYS" => "Days After the Payment",
            
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where `is_free`=0 Order By z_day Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $email_id = $row['email_id'];
                $subject = $this->dec ($row['subject']);
                $z_day = $row['z_day'];
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"email_id\", ".$email_id.")'><img src='./images/active".$row['is_active'].".png' border='0' alt='Change activity status' title='Change activity status' /></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$email_id'><img src='./images/edit.png' width='25' border='0' alt='Edit' title='Edit' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$email_id' onClick=\"return confirm ('Do you really want to delete this template?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $email_id,
                    "ROW_SUBJECT" => $subject,
                    "ROW_DAYS" => $z_day,
                    "ROW_ACTIVELINK" => $activeLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_EDITLINK" => $editLink,
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
        $this->mainTemplate = "./templates/autoresponder_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript2 ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where email_id='$id'", $this->pageUrl);
                $subject = "<input type='text' name='subject' value='".$row["subject"]."' maxlength='250' style='width: 500px;'>";
                $z_day = "<input type='text' name='z_day' value='".$row["z_day"]."' maxlength='5' style='width: 100px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'>".$row["message"]."</textarea>";

            break;

            case FORM_FROM_GP:

                
                $subject = "<input type='text' name='subject' value='".$this->GetGP ("subject")."' maxlength='250' style='width: 500px;'>";
                $z_day = "<input type='text' name='z_day' value='".$this->GetGP ("z_day")."' maxlength='5' style='width: 100px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'>".$this->GetGP ("message")."</textarea>";
                
            break;
            case FORM_EMPTY:
            default:
                
                $subject = "<input type='text' name='subject' value='' maxlength='250' style='width: 500px;'>";
                $z_day = "<input type='text' name='z_day' value='' maxlength='5' style='width: 100px;'>";
                $message = "<textarea style='width: 500px; height: 200px;' name='message'></textarea>";

                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_SUBJECT" => $subject,
            "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
            
            "MAIN_MESSAGE" => $message,
            "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
            
            "MAIN_Z_DAY" => $z_day,
            "MAIN_Z_DAY_ERROR" => $this->GetError ("z_day"),
            
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }
    
    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "New Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Active Members Autoresponder Email Templates</a> / New Template";
        $this->fill_form ("insert", FORM_EMPTY);
    }
    
    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "New Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Active Members Autoresponder Email Templates</a> / New Template";

        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));
        $z_day = $this->enc ($this->GetValidGP ("z_day", "Days after sign up", VALIDATE_INT_POSITIVE));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (subject, message, z_day, is_active, is_free) values ('$subject', '$message', '$z_day', '0', '0')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Active Members Autoresponder Email Templates</a> / Edit Template";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Active Members Autoresponder Email Templates</a> / Edit Template";
        $id = $this->GetGP ("id");
        
        $subject = $this->enc ($this->GetValidGP ("subject", "Subject", VALIDATE_NOT_EMPTY));
        $message = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));
        $z_day = $this->enc ($this->GetValidGP ("z_day", "Days after sign up", VALIDATE_INT_POSITIVE));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {

            $this->db->ExecuteSql ("Update {$this->object} Set `subject`='$subject', `message`='$message', `z_day`='$z_day' Where `email_id`='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }
    
    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Delete From {$this->object} Where `email_id`='$id'");
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

$zPage = new ZPage ("autoresponders");

$zPage->Render ();

?>
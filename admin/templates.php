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
        $this->mainTemplate = "./templates/templates.tpl";
        $this->pageTitle = "System Notifications";
        $this->pageHeader = "System Notifications";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_DESCRIPTION" => "Description",
            
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Order By emailtempl_id asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $emailtempl_id = $row['emailtempl_id'];
                $description = $row['description'];
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"emailtempl_id\", ".$emailtempl_id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$emailtempl_id'><img src='./images/edit.png' width='25' border='0' title='Edit'></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $emailtempl_id,
                    "ROW_DESCRIPTION" => $description,
                    "ROW_ACTIVELINK" => $activeLink,
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
        $this->mainTemplate = "./templates/template_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript2 ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where emailtempl_id='$id'", $this->pageUrl);
                
                $subject = "<input type='text' name='subject' value='".$row["subject"]."' maxlength='250' style='width: 500px;'>";
                $description = $this->dec ($row["description"]);
                //$message = "<textarea style='width: 500px; height: 200px;' name='message'>".$row["message"]."</textarea>";
                $message = $this->FCKeditor( "message", htmlspecialchars_decode($row["message"]) );
                $subst = $this->dec ($row["tag_descr"]);

            break;

            case FORM_FROM_GP:

                $description = $this->dec ($this->db->GetOne ("Select `description` From `{$this->object}` Where emailtempl_id='$id'"));
                $subst = $this->dec ($this->db->GetOne ("Select `tag_descr` From `{$this->object}` Where emailtempl_id='$id'"));
                
                $subject = "<input type='text' name='subject' value='".$this->GetGP ("subject")."' maxlength='250' style='width: 500px;'>";
                //$message = "<textarea style='width: 500px; height: 200px;' name='message'>".$this->GetGP ("message")."</textarea>";
                $message = $this->FCKeditor( "message", htmlspecialchars_decode($this->GetGP ("message")) );
                
            break;
            default:
            break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_SUBJECT" => $subject,
            "MAIN_SUBJECT_ERROR" => $this->GetError ("subject"),
            
            "MAIN_MESSAGE" => $message,
            "MAIN_MESSAGE_ERROR" => $this->GetError ("message"),
            
            "MAIN_DESCRIPTION" => $description,
            
            "SUBSTITUTIONS" => $subst,

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }


    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>System Notifications</a> / Edit Message";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit Template";
        $this->pageHeader = "<a href='{$this->pageUrl}'>System Notifications</a> / Edit Message";
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
        
    </script>
_ENDOFJS_;
}
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("emailtempl");

$zPage->Render ();

?>
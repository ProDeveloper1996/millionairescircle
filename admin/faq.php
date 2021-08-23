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
        $this->orderDefault = "faq_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/faq.tpl";
        $this->pageTitle = "FAQ";
        $this->pageHeader = "FAQ";
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='Add a question'><img src='./images/add.png' border='0'></a>",
            "HEAD_ID" => $this->Header_GetSortLink ("faq_id", "ID"),
            "HEAD_QUESTION" => $this->Header_GetSortLink ("question", "Question"),
            "HEAD_ANSWER" => $this->Header_GetSortLink ("answer", "Answer"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['faq_id'];
                $question = nl2br ($this->dec ($row['question']));
                $answer = nl2br ($this->dec ($row['answer']));
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"faq_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' border='0' title='Change activity status' alt='Change activity status'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' border='0' alt='Edit FAQ' title='Edit FAQ'></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this question?');\"><img src='./images/trash.png' border='0' title='Delete FAQ' alt='Delete FAQ'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $id,
                    "ROW_QUESTION" => $question,
                    "ROW_ANSWER" => $answer,
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_EDITLINK" => $editLink,
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
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/faq_details.tpl";
        $id = $this->GetGP ("id");
        switch ($source)
        {
            case FORM_FROM_DB:
                $row = $this->db->GetEntry ("Select * From {$this->object} Where faq_id=$id", $this->pageUrl);
                $question = "<textarea name='question' rows='2' style='width: 100%;'>".$row["question"]."</textarea>";
                $answer = $this->FCKeditor( "answer", htmlspecialchars_decode( $row["answer"] ) );
                break;

            case FORM_FROM_GP:
                $question = "<textarea name='question' rows='2' style='width: 100%;'>".$this->GetGP ("question")."</textarea>";
                $answer = $this->FCKeditor( "answer", htmlspecialchars_decode( $this->GetGP ("answer") ) );
                break;

            case FORM_EMPTY:
            default:
                $question = "<textarea name='question' placeholder='Type the Question here' rows='2' style='width: 100%;'></textarea>";
                $answer = $this->FCKeditor( "answer", '' );
                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_QUESTION" => $question,
            "MAIN_ANSWER" => $answer,
            "MAIN_QUESTION_ERROR" => $this->GetError ("question"),
            "MAIN_ANSWER_ERROR" => $this->GetError ("answer"),
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Add a question";
        $this->pageHeader = "<a href='{$this->pageUrl}'>FAQ</a> / Add a question";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "Add a question";
        $this->pageHeader = "<a href='{$this->pageUrl}'>FAQ</a> / Add a question";

        $question = $this->enc ($this->GetValidGP ("question", "Question", VALIDATE_NOT_EMPTY));
        $answer = $this->enc ($this->GetValidGP ("answer", "Answer", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Insert into {$this->object} (question, answer, is_active) values ('$question', '$answer', '0')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit question";
        $this->pageHeader = "<a href='{$this->pageUrl}'>FAQ</a> / Edit question";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit question";
        $this->pageHeader = "<a href='{$this->pageUrl}'>FAQ</a> / Edit question";
        $id = $this->GetGP ("id");
        $question = $this->enc ($this->GetValidGP ("question", "Question", VALIDATE_NOT_EMPTY));
        $answer = $this->enc ($this->GetValidGP ("answer", "Answer", VALIDATE_NOT_EMPTY));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update {$this->object} Set question='$question', answer='$answer' Where faq_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where faq_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Delete From {$this->object} Where faq_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_
        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("faq");

$zPage->Render ();

?>
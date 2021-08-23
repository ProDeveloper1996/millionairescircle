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

            $this->mainTemplate = "./templates/levels_forced.tpl";
            $this->pageTitle = "Forced Matrix Levels";
            $this->pageHeader = "Forced Matrix Levels";

            $total = $this->db->GetOne ("Select Count(*) From `{$this->object}`", 0);

            $addLink = "<a href='{$this->pageUrl}?ocd=new' title='Add new level'><img src='./images/add.png' border='0'></a>";
            if ($this->lic_key=='FREE' && $total>=3 )  $addLink ='';

            $this->data = array (
                    "MAIN_HEADER" => $this->pageHeader,
                    "MAIN_ADDLINK" => $addLink,
                    "HEAD_NUMBER" => "#",
                    "HEAD_TITLE" => "Title",
                    "HEAD_COST" => "Cost",
                    "HEAD_DEPTH" => "Paid Levels",
                );
            $bgcolor = "";
            if ($total > 0)
            {
                $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Order By order_index", true);
                while ($row = $this->db->FetchInArray ($result))
                {
                    $type_id = $row['type_id'];
                    $order_index = $row['order_index'];
                    $title = $this->dec ($row['title']);

                    if ($total == 1)
                    {
                        $orderLink = "&nbsp;";    
                    }
                    elseif ($order_index == $total)
                    {
                        $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$type_id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Up'></a>";
                    }
                    elseif ($order_index == 1)
                    {
                         $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$type_id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' title='Down'></a>";
                    }
                    else
                    {
                        $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$type_id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Up'></a>";
                    }
                    $cost = $row['cost'];
                    $depth = $row['depth'];
                    $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$type_id' onClick=\"return confirm ('Do you really want to delete this level?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>";
                    $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$type_id'><img src='./images/edit.png' width='25' border='0' title='Edit'></a>";
                    $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                    $this->data ['TABLE_ROW'][] = array (
                        "ROW_NUMBER" => $order_index,
                        "ROW_TITLE" => $title,
                        "ROW_COST" => $cost,
                        "ROW_DEPTH" => $depth,
                        "ROW_ORDERLINK" => $orderLink,
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

    //------------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/level_f_details.tpl";
        $id = $this->GetGP ("id");

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From `{$this->object}` Where type_id=$id", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='".$row["cost"]."' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='".$row["depth"]."' maxlength='10' style='width: 100px;'>";
                break;

            case FORM_FROM_GP:
                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='".$this->GetGP ("cost")."' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='".$this->GetGP ("depth")."' maxlength='10' style='width: 100px;'>";
                break;

            case FORM_EMPTY:
            default:
                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='0.00' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='' maxlength='10' style='width: 100px;'>";
                break;
        }
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),
            "MAIN_COST" => $cost,
            "MAIN_COST_ERROR" => $this->GetError ("cost"),
            "MAIN_DEPTH" => $depth,
            "MAIN_DEPTH_ERROR" => $this->GetError ("depth"),
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,

        );
    }

    //------------------------------------------------------------------------------
    function ocd_del ()
    {
        $type_id = $this->GetGP ("id", 0);
        $p_order = $this->db->GetOne ("Select order_index From `{$this->object}` Where type_id='$type_id'");
        $this->db->ExecuteSql ("Delete From `{$this->object}` Where type_id='$type_id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order'");
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_new ()
    {
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}`", 0);
        if ($this->lic_key=='FREE' && $total>=3 )  exit('Access denied');

        $this->pageTitle = "Add a new level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Forced Matrix Levels</a> / Add a new level";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //------------------------------------------------------------------------------
    function ocd_insert ()
    {
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}`", 0);
        if ($this->lic_key=='FREE' && $total>=3 )  exit('Access denied');

        $this->pageTitle = "Add a new level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Forced Matrix Levels</a> / Add a new level";

        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $cost = $this->enc ($this->GetValidGP ("cost", "Cost", VALIDATE_FLOAT_POSITIVE));
        $depth = $this->enc ($this->GetValidGP ("depth", "Depth", VALIDATE_INT_POSITIVE));

        if ($this->errors['err_count'] == 0)
        {
            $depth_db = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");
            if ($depth > $depth_db) $this->SetError ("depth", "The max amount of paid levels is $depth_db");
        }

        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $total = $this->db->GetOne ("Select Count(*) From `{$this->object}`", 0) + 1;
            $this->db->ExecuteSql ("Insert into `{$this->object}` (order_index, title, cost, depth) values ('$total', '$title', '$cost', '$depth')");
            $this->Redirect ($this->pageUrl);
        }
    }


    //------------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Edit level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Forced Matrix Levels</a> / Edit level";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //------------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Cycling Matrix Levels</a> / Edit level";
        $id = $this->GetGP ("id");
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $cost = $this->enc ($this->GetValidGP ("cost", "Cost", VALIDATE_FLOAT_POSITIVE));
        $depth = $this->enc ($this->GetValidGP ("depth", "Depth", VALIDATE_INT_POSITIVE));

        if ($this->errors['err_count'] == 0)
        {
            $depth_db = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");
            if ($depth > $depth_db) $this->SetError ("depth", "The max amount of paid levels is $depth_db");
        }


        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update `{$this->object}` Set title='$title', cost='$cost', depth='$depth' Where type_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select order_index From `{$this->object}` Where type_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select type_id From `{$this->object}` Where order_index='$number_next'", 0);
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index+1 Where type_id='$id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where type_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up ()
    {
        $id = $this->GetGP ("id", 0);
        $number = $this->db->GetOne ("Select order_index From `{$this->object}` Where type_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne ("Select type_id From {$this->object} Where order_index='$number_next'", 0);
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where type_id='$id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index+1 Where type_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

}
//------------------------------------------------------------------------------

$zPage = new ZPage ("types");

$zPage->Render ();

?>
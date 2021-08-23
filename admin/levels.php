<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_admin.php");
require_once("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage($object)
    {
        XPage::XPage($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list()
    {

        $this->mainTemplate = "./templates/levels_cycling.tpl";
        $this->pageTitle = "Cycling Matrix Levels";
        $this->pageHeader = "Cycling Matrix Levels";

        $total = $this->db->GetOne("Select Count(*) From `{$this->object}`", 0);

        $addLink = "<a href='{$this->pageUrl}?ocd=new' title='Add new level'><img src='./images/add.png' title='Add New Level'></a>";
        if ($this->lic_key == 'FREE' && $total >= 3) $addLink = '';


        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => $addLink,
            "HEAD_NUMBER" => "#",
            "HEAD_TITLE" => "Title",
            "HEAD_COST" => "Cost",
            "HEAD_HOST_FEE" => "Completed Payout",
            "HEAD_ENR_FEE" => "Sponsor Bonus",
            "HEAD_WIDTH" => "Width",
            "HEAD_DEPTH" => "Depth",
        );
        $bgcolor = "";
        if ($total > 0) {
            $result = $this->db->ExecuteSql("Select * From `{$this->object}` Order By order_index", true);
            while ($row = $this->db->FetchInArray($result)) {
                $type_id = $row['type_id'];
                $order_index = $row['order_index'];
                $title = $this->dec($row['title']);

                if ($total == 1) {
                    $orderLink = "&nbsp;";
                } elseif ($order_index == $total) {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$type_id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Upper this Level'></a>";
                } elseif ($order_index == 1) {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$type_id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' title='Lower this Level'></a>";
                } else {
                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$type_id'><img src='./images/arrow_up.png' align='absmiddle' width='25' border='0' title='Upper this Level'></a>";
                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$type_id'><img src='./images/arrow_down.png' align='absmiddle' width='25' border='0' title='Lower this Level'></a>";
                }
                $cost = $row['cost'];
                $host_fee = $row['host_fee'];
                $enr_fee = $row['enr_fee'];
                $width = $row['width'];
                $depth = $row['depth'];
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$type_id' onClick=\"return confirm ('Do you really want to delete this level?');\"><img src='./images/trash.png' width='25' border='0' title='Delete this Level'></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$type_id'><img src='./images/edit.png' width='25' border='0' title='Edit this Level'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array(
                    "ROW_NUMBER" => $order_index,
                    "ROW_TITLE" => $title,
                    "ROW_COST" => $cost,
                    "ROW_HOST_FEE" => $host_fee,
                    "ROW_ENR_FEE" => $enr_fee,
                    "ROW_WIDTH" => $width,
                    "ROW_DEPTH" => $depth,
                    "ROW_ORDERLINK" => $orderLink,
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor,
                    'ROW_ADMIN_FEE' => $row['admin_fee']
                );
            }
            $this->db->FreeSqlResult($result);
        } else {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array(
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }

    //------------------------------------------------------------------------------
    function fill_form($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/level_c_details.tpl";
        $id = $this->GetGP("id");

        switch ($source) {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry("Select * From `{$this->object}` Where type_id=$id", $this->pageUrl);
                $title = "<input type='text' name='title' value='" . $row["title"] . "' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='" . $row["cost"] . "' maxlength='10' style='width: 100px;'>";
                $host_fee = "<input type='text' name='host_fee' value='" . $row["host_fee"] . "' maxlength='10' style='width: 100px;'>";
                $enr_fee = "<input type='text' name='enr_fee' value='" . $row["enr_fee"] . "' maxlength='10' style='width: 100px;'>";
                $admin_fee = "<input type='text' name='admin_fee' value='" . $row["admin_fee"] . "' maxlength='10' style='width: 100px;'>";
                $width = "<input type='text' name='width' value='" . $row["width"] . "' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='" . $row["depth"] . "' maxlength='10' style='width: 100px;'>";
                break;

            case FORM_FROM_GP:
                $title = "<input type='text' name='title' value='" . $this->GetGP("title") . "' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='" . $this->GetGP("cost") . "' maxlength='10' style='width: 100px;'>";
                $host_fee = "<input type='text' name='host_fee' value='" . $this->GetGP("host_fee") . "' maxlength='10' style='width: 100px;'>";
                $enr_fee = "<input type='text' name='enr_fee' value='" . $this->GetGP("enr_fee") . "' maxlength='10' style='width: 100px;'>";
                $admin_fee = "<input type='text' name='admin_fee' value='" . $this->GetGP("admin_fee") . "' maxlength='10' style='width: 100px;'>";
                $width = "<input type='text' name='width' value='" . $this->GetGP("width") . "' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='" . $this->GetGP("depth") . "' maxlength='10' style='width: 100px;'>";
                break;

            case FORM_EMPTY:
            default:
                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                $cost = "<input type='text' name='cost' value='0.00' maxlength='10' style='width: 100px;'>";
                $host_fee = "<input type='text' name='host_fee' value='0.00' maxlength='10' style='width: 100px;'>";
                $enr_fee = "<input type='text' name='enr_fee' value='0.00' maxlength='10' style='width: 100px;'>";
                $admin_fee = "<input type='text' name='admin_fee' value='0.00' maxlength='10' style='width: 100px;'>";
                $width = "<input type='text' name='width' value='' maxlength='10' style='width: 100px;'>";
                $depth = "<input type='text' name='depth' value='' maxlength='10' style='width: 100px;'>";
                break;
        }
        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_CANCEL_URL" => $this->pageUrl,

            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError("title"),
            "MAIN_COST" => $cost,
            "MAIN_COST_ERROR" => $this->GetError("cost"),
            "MAIN_HOST_FEE" => $host_fee,
            "MAIN_HOST_FEE_ERROR" => $this->GetError("host_fee"),
            "MAIN_ENR_FEE" => $enr_fee,
            "MAIN_ENR_FEE_ERROR" => $this->GetError("enr_fee"),
            "MAIN_ADMIN_FEE" => $admin_fee,
            "MAIN_ADMIN_FEE_ERROR" => $this->GetError("admin_fee"),
            "MAIN_WIDTH" => $width,
            "MAIN_WIDTH_ERROR" => $this->GetError("width"),
            "MAIN_DEPTH" => $depth,
            "MAIN_DEPTH_ERROR" => $this->GetError("depth"),
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,

        );
    }

    //------------------------------------------------------------------------------
    function ocd_del()
    {
        $type_id = $this->GetGP("id", 0);
        $p_order = $this->db->GetOne("Select order_index From `{$this->object}` Where type_id='$type_id'");
        $this->db->ExecuteSql("Delete From `{$this->object}` Where type_id='$type_id'");
        $this->db->ExecuteSql("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order'");
        $this->Redirect($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_new()
    {
        $total = $this->db->GetOne("Select Count(*) From `{$this->object}`", 0);
        if ($this->lic_key == 'FREE' && $total >= 3) exit('Access denied');

        $this->pageTitle = "Add a new level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Cycling Matrix Levels</a> / Add a new level";
        $this->fill_form("insert", FORM_EMPTY);
    }

    //------------------------------------------------------------------------------
    function ocd_insert()
    {
        $total = $this->db->GetOne("Select Count(*) From `{$this->object}`", 0);
        if ($this->lic_key == 'FREE' && $total >= 3) exit('Access denied');

        $this->pageTitle = "Add a new level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Cycling Matrix Levels</a> / Add a new level";

        $title = $this->enc($this->GetValidGP("title", "Title", VALIDATE_NOT_EMPTY));
        $cost = $this->enc($this->GetValidGP("cost", "Cost", VALIDATE_FLOAT_POSITIVE));
        $host_fee = $this->enc($this->GetValidGP("host_fee", "Completed Fee", VALIDATE_FLOAT_POSITIVE));
        $enr_fee = $this->enc($this->GetValidGP("enr_fee", "Sponsor Bonus", VALIDATE_FLOAT_POSITIVE));
        $admin_fee = $this->enc($this->GetValidGP("admin_fee", "Admin Fee", VALIDATE_FLOAT_POSITIVE));
        $width = $this->enc($this->GetValidGP("width", "Width", VALIDATE_INT_POSITIVE));
        $depth = $this->enc($this->GetValidGP("depth", "Depth", VALIDATE_INT_POSITIVE));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form("insert", FORM_FROM_GP);
        } else {
            $total = $this->db->GetOne("Select Count(*) From `{$this->object}`", 0) + 1;
            $this->db->ExecuteSql("Insert into `{$this->object}` (order_index, title, cost, host_fee, enr_fee, width, depth, admin_fee) values ('$total', '$title', '$cost', '$host_fee', '$enr_fee', '$width', '$depth', '$admin_fee')");
            $this->Redirect($this->pageUrl);
        }
    }


    //------------------------------------------------------------------------------
    function ocd_edit()
    {
        $this->pageTitle = "Edit level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Cycling Matrix Levels</a> / Edit level";
        $this->fill_form("update", FORM_FROM_DB);
    }

    //------------------------------------------------------------------------------
    function ocd_update()
    {
        $this->pageTitle = "Edit level";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Cycling Matrix Levels</a> / Edit level";
        $id = $this->GetGP("id");
        $title = $this->enc($this->GetValidGP("title", "Title", VALIDATE_NOT_EMPTY));
        $cost = $this->enc($this->GetValidGP("cost", "Cost", VALIDATE_FLOAT_POSITIVE));
        $host_fee = $this->enc($this->GetValidGP("host_fee", "Completed Fee", VALIDATE_FLOAT_POSITIVE));
        $enr_fee = $this->enc($this->GetValidGP("enr_fee", "Sponsor Bonus", VALIDATE_FLOAT_POSITIVE));
        $admin_fee = $this->enc($this->GetValidGP("admin_fee", "Admin Fee", VALIDATE_FLOAT_POSITIVE));
        $width = $this->enc($this->GetValidGP("width", "Width", VALIDATE_INT_POSITIVE));
        $depth = $this->enc($this->GetValidGP("depth", "Depth", VALIDATE_INT_POSITIVE));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form("update", FORM_FROM_GP);
        } else {
            $this->db->ExecuteSql("Update `{$this->object}` Set title='$title', cost='$cost', host_fee='$host_fee', enr_fee='$enr_fee', width='$width', depth='$depth', admin_fee='$admin_fee' Where type_id='$id'");
            $this->Redirect($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_down()
    {
        $id = $this->GetGP("id", 0);
        $number = $this->db->GetOne("Select order_index From `{$this->object}` Where type_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne("Select type_id From `{$this->object}` Where order_index='$number_next'", 0);
        $this->db->ExecuteSql("Update `{$this->object}` Set order_index=order_index+1 Where type_id='$id'");
        $this->db->ExecuteSql("Update `{$this->object}` Set order_index=order_index-1 Where type_id='$id_next'");
        $this->Redirect($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up()
    {
        $id = $this->GetGP("id", 0);
        $number = $this->db->GetOne("Select order_index From `{$this->object}` Where type_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne("Select type_id From {$this->object} Where order_index='$number_next'", 0);
        $this->db->ExecuteSql("Update `{$this->object}` Set order_index=order_index-1 Where type_id='$id'");
        $this->db->ExecuteSql("Update `{$this->object}` Set order_index=order_index+1 Where type_id='$id_next'");
        $this->Redirect($this->pageUrl);
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("types");

$zPage->Render();

?>
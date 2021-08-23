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
        $this->orderDefault = "cash_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/cash.tpl";
        $this->pageTitle = "Cash History";
        $this->pageHeader = "Cash History";
        $filter = $this->GetGP ("filter");
        if ($filter == 1)
        {
            $cash_to_id = $this->enc ($this->GetGP ("cash_to_id"));
            $cash_from_id = $this->enc ($this->GetGP ("cash_from_id"));
            $payment_id = $this->enc ($this->GetGP ("payment_id"));
            
            $filterDateDayBegin = $this->GetGP ("filterDateDayBegin");
            $filterDateMonthBegin = $this->GetGP ("filterDateMonthBegin");
            $filterDateYearBegin = $this->GetGP ("filterDateYearBegin");
            $filterDateDayEnd = $this->GetGP ("filterDateDayEnd");
            $filterDateMonthEnd = $this->GetGP ("filterDateMonthEnd");
            $filterDateYearEnd = $this->GetGP ("filterDateYearEnd");
            $this->SaveStateValue ("filterDateBegin", mktime(0, 0, 0, $filterDateMonthBegin, $filterDateDayBegin, $filterDateYearBegin));
            $this->SaveStateValue ("filterDateEnd", mktime(23, 59, 59, $filterDateMonthEnd, $filterDateDayEnd, $filterDateYearEnd));
            $this->SaveStateValue ("cash_to_id", $cash_to_id);
            $this->SaveStateValue ("cash_from_id", $cash_from_id);
            $this->SaveStateValue ("payment_id", $payment_id);
        }
        $filterDateBegin = $this->GetStateValue ("filterDateBegin", 0);
        $filterDateEnd = $this->GetStateValue ("filterDateEnd", 0);
        $cash_to_id = $this->GetStateValue ("cash_to_id");
        $cash_from_id = $this->GetStateValue ("cash_from_id");
        $payment_id = $this->GetStateValue ("payment_id");
        $filterDateDayBegin = ($filterDateBegin != 0) ? date ("d", $filterDateBegin) : "";
        $filterDateMonthBegin = ($filterDateBegin != 0) ? date ("m", $filterDateBegin) : "";
        $filterDateYearBegin = ($filterDateBegin != 0) ? date ("Y", $filterDateBegin) : date ("Y", time())-1;
        $filterDateDayEnd = ($filterDateEnd != 0) ? date ("d", $filterDateEnd) : "";
        $filterDateMonthEnd = ($filterDateEnd != 0) ? date ("m", $filterDateEnd) : "";
        $filterDateYearEnd = ($filterDateEnd != 0) ? date ("Y", $filterDateEnd) : "";
        $sql_select = "";
        if ($cash_to_id != "") $sql_select .= " And to_id='$cash_to_id'";
        if ($cash_from_id != "") $sql_select .= " And from_id='$cash_from_id'";
        if ($payment_id != "") $sql_select .= " And payment_id='$payment_id'";
        if ($filterDateBegin != 0) $sql_select .= " And cash_date>$filterDateBegin And cash_date<$filterDateEnd";
        $filter = "";
        $filter .= "Date from ";
        $filter .= getDaySelect ($filterDateDayBegin, "filterDateDayBegin");
        $filter .= getMonthSelect ($filterDateMonthBegin, "filterDateMonthBegin");
        $filter .= getYearSelect ($filterDateYearBegin, "filterDateYearBegin");
        $filter .= " to ";
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd");
        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where 1 $sql_select");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,
            "CASH_TO_ID" => "<input type='text' name='cash_to_id' value='$cash_to_id' style='width:60px;' maxlength='6'>",
            "CASH_FROM_ID" => "<input type='text' name='cash_from_id' value='$cash_from_id' style='width:60px;' maxlength='6'>",
            "CASH_PAYMENT_ID" => "<input type='text' name='payment_id' value='$payment_id' style='width:60px;' maxlength='6'>",            
            "HEAD_CASH_ID" => $this->Header_GetSortLink ("cash_id", "ID"),
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", "Amount"),
            "HEAD_TO" => $this->Header_GetSortLink ("to_id", "To Member"),
            "HEAD_FROM" => $this->Header_GetSortLink ("from_id", "From Member"),
            "HEAD_DATE" => $this->Header_GetSortLink ("cash_date", "Date"),
            "HEAD_TYPE" => $this->Header_GetSortLink ("type_cash", "Matrix Level"),
            "HEAD_DESCRIPTION" => $this->Header_GetSortLink ("descr", "Description"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        $orderBy = "cash_id";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Where 1 $sql_select Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $cash_id = $row['cash_id'];
                $to_id = $row['to_id'];
                $name = $this->dec ($this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$to_id'", ""));
                $name .= " (ID #$to_id)";
                $from_id = $row['from_id'];
                
                $from_name = "System";
                if ($from_id > 0)
                {
                    $from_name = $this->dec ($this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$from_id'", ""));
                    $from_name .= " (ID #$from_id)";
                }
                
                
                $amount = $row['amount'];
                $date = date ("d M Y H:i", $row['cash_date']);
                $type = $row['type_cash'];
                $description = $row['descr'];
//                $description .= ($description == "For matrix")? " (Level: #$type, Number: #$from_id)" : "";
                $description .= ($description == "For completed matrix")? " (Level: #$type, Number: #$from_id)" : "";
                $description .= ($description == "Sponsor Bonus")? " (From #$from_id)" : "";
                $description .= ($description == "To sponsor")? " (#$from_id completed matrix of #$type level)" : "";
                $description .= ($description == "For re-cycling")? " (Level #$type)" : "";

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$cash_id' onClick=\"return confirm ('Do you really want to delete this commissions?');\"><img src='./images/trash.png' width='25' border='0' title='Delete'></a>";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_AMOUNT" => $amount,
                    "ROW_CASH_ID" => $cash_id,
                    "ROW_DATE" => $date,
                    "ROW_DESCRIPTION" => $description,

                    "ROW_TO" => $name,
                    "ROW_FROM" => $from_name,
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
    function ocd_del ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Delete From `cash` Where cash_id='$id'");
        $this->Redirect ($this->pageUrl);
    }

}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cash");

$zPage->Render ();

?>
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
        $this->orderDefault = "cash_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
		  GLOBAL $dict;
		  
        $this->mainTemplate = "./templates/cash.tpl";
        $this->pageTitle = $dict['Cash_pageTitle'];
        $this->pageHeader = $dict['Cash_pageTitle'];
        $currency = $this->currency_synbol;
        $filter = $this->GetGP ("filter");
        if ($filter == 1)
        {
            $member_id = $this->GetID ("member_id");
            $filterDateDayBegin = $this->GetGP ("filterDateDayBegin");
            $filterDateMonthBegin = $this->GetGP ("filterDateMonthBegin");
            $filterDateYearBegin = $this->GetGP ("filterDateYearBegin");
            $filterDateDayEnd = $this->GetGP ("filterDateDayEnd");
            $filterDateMonthEnd = $this->GetGP ("filterDateMonthEnd");
            $filterDateYearEnd = $this->GetGP ("filterDateYearEnd");
            $this->SaveStateValue ("filterDateBegin", mktime(0, 0, 0, $filterDateMonthBegin, $filterDateDayBegin, $filterDateYearBegin));
            $this->SaveStateValue ("filterDateEnd", mktime(23, 59, 59, $filterDateMonthEnd, $filterDateDayEnd, $filterDateYearEnd));
            $this->SaveStateValue ("member_id", $member_id);
        }
        $filterDateBegin = $this->GetStateValue ("filterDateBegin", 0);
        $filterDateEnd = $this->GetStateValue ("filterDateEnd", 0);
        $member_id = $this->GetStateValue ("member_id");

        $filterDateDayBegin = ($filterDateBegin != 0) ? date ("d", $filterDateBegin) : "";
        $filterDateMonthBegin = ($filterDateBegin != 0) ? date ("m", $filterDateBegin) : "";
        $filterDateYearBegin = ($filterDateBegin != 0) ? date ("Y", $filterDateBegin) : date ("Y", time())-1;
        $filterDateDayEnd = ($filterDateEnd != 0) ? date ("d", $filterDateEnd) : "";
        $filterDateMonthEnd = ($filterDateEnd != 0) ? date ("m", $filterDateEnd) : "";
        $filterDateYearEnd = ($filterDateEnd != 0) ? date ("Y", $filterDateEnd) : "";
        $sql_select = "";
        if ($member_id != 0) $sql_select .= " And from_id='$member_id'";
        if ($filterDateBegin != 0) $sql_select .= " And cash_date>$filterDateBegin And cash_date<$filterDateEnd";
        $filter = "";
        $filter .= "<div class='col-xs-12 col-sm-11 col-md-4'><div class='row text-center'>";//$dict['Cash_Datefrom'];
        $filter .= getDaySelect ($filterDateDayBegin, "filterDateDayBegin");
        $filter .= getMonthSelect ($filterDateMonthBegin, "filterDateMonthBegin");
        $filter .= getYearSelect ($filterDateYearBegin, "filterDateYearBegin");
        $filter .= "
            </div>
                            </div>
                            <div class='col-xs-12 col-sm-1 col-md-1'>
                                <div class='row text-center'>
                                    <i class='fa fa-minus'></i>
                                </div>
                            </div>
                            <div class='col-xs-12 col-sm-9 col-md-4'>
                                <div class='row text-center'>
        ";//$dict['Cash_to'];
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd");
        $filter .= "</div></div>";
        $cash_out = $this->GetGp("sum_out", 0);
        $total_cash = $this->db->GetOne ("Select SUM(amount) From `{$this->object}` Where to_id='{$this->member_id}'", "0.00");

        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where 1 $sql_select And to_id='{$this->member_id}'", 0);
        $total_tab = "";
        $min_cash_out = $this->db->GetSetting ("MinCashOut", 0);
        
        $processor = $this->db->GetOne ("Select processor From `members` Where member_id='{$this->member_id}'", 0);
        $account_id = $this->db->GetOne ("Select account_id From `members` Where member_id='{$this->member_id}'", "");
        
        if ($total_cash >= $min_cash_out And $processor > 0 And $account_id != "")
        {
            $total_tab = "<form action='cash_out.php' name='cash_o' method='POST'><tr style='height:35px;'><td align='center' bgcolor='#475567' class='b_border' colspan='6'>{$dict['Cash_Totalamount']}: $currency<b>$total_cash</b>. <input type='submit' class='btn btn-form';' value='{$dict['Cash_MakeWithdrawalRequest']}'></td></tr></form>";
        }
        else
        {
            $total_tab = "<tr style='height:35px;'><td align='center' bgcolor='#475567' class='b_border' colspan='6'>{$dict['Cash_Totalamount']}: $currency<b>$total_cash</b>.</td></tr></form>";
        }
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,
            "TOTAL_CASH" => $total_tab,
            "MEMBER_ID" => $member_id,
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", $dict['Cash_Amount']),
            "HEAD_DATE" => $this->Header_GetSortLink ("cash_date", $dict['Cash_Date']),
            "HEAD_DESCRIPTION" => $this->Header_GetSortLink ("descr", $dict['Cash_Description']),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where 1 $sql_select And to_id='{$this->member_id}' Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $from_id = $row['from_id'];
                $amount = $row['amount'];
                $date = date ("d M Y H:i", $row['cash_date']);
                $type = $row['type_cash'];
                $description = $row['descr'];

                $description .= ($description == "For completed matrix")? " ({$dict['Cash_Level']}: #$type, {$dict['Cash_Number']}: #$from_id)" : "";
                $description .= ($description == "Sponsor Bonus")? " ({$dict['Cash_From']} #$from_id)" : "";
                $description .= ($description == "To sponsor")? " (#$from_id {$dict['Cash_cmx']} #$type {$dict['Cash_cmx1']})" : "";
                $description .= ($description == "For re-cycling")? " ({$dict['Cash_Level']} #$type)" : "";

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_AMOUNT" => $amount,
                    "ROW_DATE" => $date,
                    "ROW_DESCRIPTION" => $description,
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
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cash");

$zPage->Render ();

?>
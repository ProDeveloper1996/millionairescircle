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
        $this->orderDefault = "payins_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
		  $this->mainTemplate = "./templates/payments.tpl";
        $this->pageTitle = $dict['PM_pageTitle3'];
        $this->pageHeader = $dict['PM_pageTitle3'];
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
        }
        $filterDateBegin = $this->GetStateValue ("filterDateBegin", 0);
        $filterDateEnd = $this->GetStateValue ("filterDateEnd", 0);

        $filterDateDayBegin = ($filterDateBegin != 0) ? date ("d", $filterDateBegin) : "";
        $filterDateMonthBegin = ($filterDateBegin != 0) ? date ("m", $filterDateBegin) : "";
        $filterDateYearBegin = ($filterDateBegin != 0) ? date ("Y", $filterDateBegin) : date ("Y", time())-1;
        $filterDateDayEnd = ($filterDateEnd != 0) ? date ("d", $filterDateEnd) : "";
        $filterDateMonthEnd = ($filterDateEnd != 0) ? date ("m", $filterDateEnd) : "";
        $filterDateYearEnd = ($filterDateEnd != 0) ? date ("Y", $filterDateEnd) : "";
        $sql_select = "";
        if ($filterDateBegin != 0) $sql_select .= " And z_date>$filterDateBegin And z_date<$filterDateEnd";
        $filter = "";
        $filter .= "<div class='col-xs-12 col-sm-11 col-md-4'><div class='row text-center'>";//$dict['PM_Datefrom'];
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
        ";//$dict['PM_to'];
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd");
        $filter .= "</div></div>";
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where 1 $sql_select And member_id='{$this->member_id}'", 0);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", $dict['PM_Amount']),
            "HEAD_DATE" => $this->Header_GetSortLink ("z_date", $dict['PM_Date']),
            "HEAD_TRANSACTION_ID" => $this->Header_GetSortLink ("transaction_id", $dict['PM_TransactionID']),
            "HEAD_PROCESSOR" => $this->Header_GetSortLink ("processor", $dict['PM_Processor']),

            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where 1 $sql_select And member_id='{$this->member_id}' Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $amount = $row['amount'];
                $date = date ("d M Y H:i", $row['z_date']);
                $transaction_id = $row['transaction_id'];
                $processor_id = $row['processor'];
                
                if ($processor_id == -2) 
                    $processor = $dict['PM_mess7'];
                else
                    $processor = $this->dec ($this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", $dict['PM_Manual']));

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_AMOUNT" => $amount,
                    "ROW_DATE" => $date,
                    "ROW_TRANSACTION_ID" => $transaction_id,
                    "ROW_PROCESSOR" => $processor,
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

$zPage = new ZPage ("payins");

$zPage->Render ();

?>
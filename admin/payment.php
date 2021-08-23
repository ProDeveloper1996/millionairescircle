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
        $this->orderDefault = "payins_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/payment.tpl";
        $this->pageTitle = "Payment History";
        $this->pageHeader = "Payment History";
        $filter = $this->GetGP ("filter", 0);
        if ($filter == 1)
        {
            $member_id = $this->GetGP ("member_id");
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
        if ($member_id != 0) $sql_select .= " And member_id='$member_id'";
        if ($filterDateBegin != 0) $sql_select .= " And z_date>$filterDateBegin And z_date<$filterDateEnd";


        $filter = "";
        $filter .= "Date from ";
        $filter .= getDaySelect ($filterDateDayBegin, "filterDateDayBegin");
        $filter .= getMonthSelect ($filterDateMonthBegin, "filterDateMonthBegin");
        $filter .= getYearSelect ($filterDateYearBegin, "filterDateYearBegin");
        $filter .= " to ";
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd");

        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where 1 $sql_select");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,
            "MEMBER_ID" => "<input type='text' name='member_id' value='$member_id' style='width:40px;' maxlength='6'>",
            "HEAD_PAYMENT_ID" => $this->Header_GetSortLink ("payins_id", "ID"),
            "HEAD_MEMBER_ID" => $this->Header_GetSortLink ("member_id", "Member (ID)"),
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", "Amount"),
            "HEAD_PROCESSOR" => $this->Header_GetSortLink ("processor", "Processor"),
            "HEAD_DESCRIPTION" => $this->Header_GetSortLink ("description", "Description"),
            "HEAD_TR" => $this->Header_GetSortLink ("transaction_id", "Transaction ID"),
            "HEAD_DATE" => $this->Header_GetSortLink ("z_date", "Date"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where 1 $sql_select Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $payins_id = $row['payins_id'];
                $member_id = $row['member_id'];
                $description = $this->dec ($row['description']);

                $processor_id = $row['processor'];
                $transaction_id = $row['transaction_id'];

                if ($processor_id == -2) 
                    $processor = "Payment from account cash balance";
                else
                    $processor = $this->dec ($this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", "Manual"));

                $fname = $this->db->GetOne ("Select first_name From members Where member_id='$member_id'");
                $lname = $this->db->GetOne ("Select last_name From members Where member_id='$member_id'");
                $member = $fname." ".$lname." (".$member_id.")";
                $amount = $row['amount'];
                $date = date ("d M Y H:i", $row['z_date']);
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$payins_id' onClick=\"return confirm ('Do you really want to delete this payment with commissions?');\"><img src='./images/trash.png' width='25' border='0' title='Delete' alt='Delete'></a>";
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_PAYMENT_ID" => $payins_id,
                    "ROW_MEMBER_ID" => "<a href='{$this->pageUrl}?ocd=tomem&id=$member_id' target='_blank'>".$member."</a>",
                    "ROW_AMOUNT" => $amount,
                    "ROW_PROCESSOR" => $processor,
                    "ROW_DATE" => $date,
                    "ROW_DESCRIPTION" => $description,
                    "ROW_TR" => $transaction_id,
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
        $member_id = $this->db->GetOne ("Select member_id From `payins` Where payins_id='$id'");
        $product_id = $this->db->GetOne ("Select product_id From `payins` Where payins_id='$id'", 0);
        $this->db->ExecuteSql ("Delete From `payins` Where payins_id='$id'");
        
        if ($product_id == 0)
        {
            $cycling = $this->db->GetSetting ("cycling", 0);
            if ($cycling == 1)
            {     
                $this->db->ExecuteSql ("Update `members` Set m_level=0 Where member_id='$member_id'");
            }
            else
            {
                $this->db->ExecuteSql ("Update `members` Set m_level=1 Where member_id='$member_id'");
                $this->db->ExecuteSql ("Delete From `cash` Where payment_id='$id'");
            }
            
        }
        $this->Redirect ($this->pageUrl);        
    }
//------------------------------------------------------------------------------    
function ocd_tomem ()
    {
        $id = $this->GetGP ("id", 0);
        $_SESSION['MemberID'] = $id;
        
        $this->Redirect ("../member/payments.php");
        
    }    
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("payins");

$zPage->Render ();

?>
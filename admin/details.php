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
        $this->orderDefault = "payment_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/details.tpl";
        $this->pageTitle = "Payments Report";
        $this->pageHeader = "Payments Report";

        $this->SaveStateValue ("check_month", $this->GetGP ("check_month"));
        $this->SaveStateValue ("check_day", $this->GetGP ("check_day"));

        if ($this->GetGP ("DateMonth") != "") $this->SaveStateValue ("DateMonth", $this->GetGP ("DateMonth"));
        if ($this->GetGP ("DateYear") != "") $this->SaveStateValue ("DateYear", $this->GetGP ("DateYear"));
        if ($this->GetGP ("DateDay") != "") $this->SaveStateValue ("DateDay", $this->GetGP ("DateDay"));


        $DateYear = $this->GetStateValue ("DateYear", date ("Y"));
        $DateMonth = $this->GetStateValue ("DateMonth", date ("M"));
        $DateDay = $this->GetStateValue ("DateDay", date ("d"));

        $check_month_t = ($this->GetStateValue ("check_month", "") == 1)? "checked" : "";
        $check_day_t = ($this->GetStateValue ("check_day", "") == 1)? "checked" : "";
        if ($check_day_t == 'checked') $check_month_t = 'checked';

        $DateYear_n = getYearSelect ($DateYear, "DateYear");
        $DateMonth_n = getMonthSelect ($DateMonth, "DateMonth");
        $DateDay_n = getDaySelect ($DateDay, "DateDay");


        $main_date_select = "<table class='w_border' border='0' cellspacing='0' cellpadding='3' bgcolor='#ccffff'><tr><td class='w_border' style='padding-left:10px;padding-right:10px;'>".$DateYear_n."&nbsp;</td><td class='w_border' style='padding-left:10px;padding-right:10px;'>".$DateMonth_n."&nbsp;<input type='checkbox' name='check_month' value='1' $check_month_t></td><td class='w_border' style='padding-left:10px;padding-right:10px;'>".$DateDay_n."&nbsp;<input type='checkbox' name='check_day' value='1' $check_day_t></td><td class='w_border' style='padding-left:10px;padding-right:10px;'><input type='submit' value=' Ok '></td></tr></table>";

        if ($check_day_t == "checked")
        {
            $startDate = mktime (0, 0, 0, $DateMonth, $DateDay, $DateYear);
            $finishDate =  mktime (23, 59, 59, $DateMonth, $DateDay, $DateYear);
            $sign_date = date ('M - d - Y', $startDate); 
        }
        elseif ($check_month_t == "checked" And $check_day_t == "")
        {
            $startDate =  mktime (0, 0, 0, $DateMonth, 1, $DateYear);
            $finishDate =  mktime (23, 59, 59, $DateMonth, 31, $DateYear);
            $sign_date = date ('M - Y', $startDate);
        }
        else
        {
            $startDate =  mktime (0, 0, 0, 1, 1, $DateYear);
            $finishDate =  mktime (23, 59, 59, 12, 31, $DateYear);
            $sign_date = date ('Y', $startDate)." year";    
        }
     
        $total_payments = $this->db->GetOne ("Select Count(*) From `payins` Where z_date>='$startDate' And z_date<='$finishDate'", 0);
        $cash_in_payments = $this->db->GetOne ("Select SUM(amount) From `payins` Where z_date>='$startDate' And z_date<='$finishDate'", "0.00");
        
        $total_active_earnings = $this->db->GetOne ("Select SUM(amount) From `cash` Where amount>0 And from_id>0 And cash_date>='$startDate' And cash_date<='$finishDate'", "0.00");
        
        $pending_cash_out = $this->db->GetOne ("Select SUM(amount) From `cash_out` Where status=0 And transfer_date>='$startDate' And transfer_date<='$finishDate'", "0.00");
        $completed_cash_out = $this->db->GetOne ("Select SUM(amount) From `cash_out` Where status=1 And transfer_date>='$startDate' And transfer_date<='$finishDate'", "0.00");
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_DATE_SELECT" => $main_date_select,
            
            "TOTAL_PAYMENTS" => $total_payments,
            "TOTAL_IN_PAYMENTS" => $cash_in_payments,
            
            "TOTAL_ACTIVE_EARNINGS" => $total_active_earnings,
            
            "TOTAL_PENDING_CASH_OUT" => $pending_cash_out,
            "TOTAL_COMPLETED_CASH_OUT" => $completed_cash_out,
            "SIGNS" => $sign_date,
            
        );
        
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("details");

$zPage->Render ();

?>
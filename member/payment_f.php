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
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['PM_pageTitle'];
        $this->pageHeader = $dict['PM_pageTitle'];
        $this->mainTemplate = "./templates/payment_f.tpl";
        $currency = $this->currency_synbol;
        $member_id = $this->member_id;
        
        $mess = "";
        if ($this->GetGP ("res", "") == "no") $mess = $dict['PM_mess1'];
        if ($this->GetGP ("res", "") == "noa") $mess = $dict['PM_mess1'];
        
        $cycling = $this->db->GetSetting ("cycling", 1);
        if ($cycling == 1) $this->Redirect ("payment.php");
        
        $current_level = $this->db->GetOne ("Select `m_level` From `members` Where `member_id`='$member_id'");
        $reg_date = $this->db->GetOne ("Select `reg_date` From `members` Where `member_id`='$member_id'");
        $quant_pay = $this->db->GetOne ("Select `quant_pay` From `members` Where `member_id`='$member_id'");
        
        $thisTime = time ();
        $turn_date = $this->db->GetSetting ("PaymentModeDate");
        $payPeriod = $this->db->GetSetting ("payPeriod");
        $warnPeriod = $this->db->GetSetting ("warnPeriod");
        $monthPeriod = $this->db->GetSetting ("monthPeriod");
        $tempore = max ($reg_date, $turn_date) + $quant_pay * $monthPeriod * 24 * 3600;

        $description = "";
        $count = $this->db->GetOne ("Select COUNT(*) From `types` Where cost>0", 0);
        if ($thisTime < $tempore And $count > 1)
        { 
            $description = $dict['PM_Text1'].date ("d M Y", $tempore)."";
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "LEVEL" => $this->select_level (0),
            "PROCESSOR" => $this->select_processor (0),
            "MAIN_MESSAGE" => $mess,
            "DESCRIPTION" => $description,
        );

    }

    //--------------------------------------------------------------------------
    function select_processor ($processor_id = 0)
    {
        GLOBAL $dict;
		  $toRet = "<select name='processor' id='processor'> \r\n";
        $result = $this->db->ExecuteSql ("Select * From `processors` Where is_active=1 Order By name");
        $selected = ($processor_id == 0)? "selected" : "";
        $toRet .= "<option value='0' $selected>{$dict['PM_Selectprocessor']}</option>";
        
        
        //from cash account
        $useBalance = $this->db->GetSetting ("useBalance");
        if ($useBalance == 1)
        {
            $min_amount = $this->db->GetOne ("Select MIN(cost) From `types` Where cost>0");
            $total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
            
            $total_cash = sprintf ("%01.2f", $total_cash);
            
            if ($total_cash >= $min_amount)
            $toRet .= "<option value='-1'>{$dict['PM_mess3']}".$total_cash.")</option>";
        }
        
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['processor_id'];
            $fee = $row['fee'];
            $selected = ($id == $processor_id)? "selected" : "";
            $toRet .= "<option value='".$id."' $selected>".$row['name']." ({$dict['PM_fee']}: $fee%)</option>";
        }
        return $toRet."</select>\r\n";
    }

    //--------------------------------------------------------------------------
    function select_level ($m_level = 0)
    {
        GLOBAL $dict;
        $member_id = $this->member_id;
        $current_level = $this->db->GetOne ("Select `m_level` From `members` Where `member_id`='$member_id'");
        $reg_date = $this->db->GetOne ("Select `reg_date` From `members` Where `member_id`='$member_id'");
        $quant_pay = $this->db->GetOne ("Select `quant_pay` From `members` Where `member_id`='$member_id'");
        $currency = $this->currency_synbol;
        
        $thisTime = time ();
        $turn_date = $this->db->GetSetting ("PaymentModeDate");
        $payPeriod = $this->db->GetSetting ("payPeriod");
        $warnPeriod = $this->db->GetSetting ("warnPeriod");
        $monthPeriod = $this->db->GetSetting ("monthPeriod");
        $tempore = max ($reg_date, $turn_date) + $quant_pay * $monthPeriod * 24 * 3600;
        
        $payperiod =  ($thisTime > $tempore)? true : false; 
        
        $toRet = "<select name='m_level'> \r\n";
        $selected = ($m_level == 0)? "selected" : "";
        $toRet .= "<option value='0' $selected>{$dict['PM_Selectlevel']}</option>";
        
        $sqlAdd = ($payperiod)? "" : " And order_index='$current_level' "; 

        $result = $this->db->ExecuteSql ("Select * From `types` Where 1 And cost>0 $sqlAdd Order By order_index");
        while ($row = $this->db->FetchInArray ($result))
        {
            $order_index = $row['order_index'];
            $cost = $row['cost'];
            
            $selected = ($order_index == $m_level)? "selected" : "";
            $toRet .= "<option value='".$order_index."' $selected>".$row['title']." ({$dict['PM_cost']}: $currency$cost)</option>";
        }
        return $toRet."</select>\r\n";
    }

    //--------------------------------------------------------------------------
    function ocd_prepayment ()
    {
        GLOBAL $dict;
		  $processor = $this->GetID ("processor");
        $m_level =  $this->GetID ("m_level");
        $member_id = $this->member_id;

        $product_name = $this->dec ($this->db->GetSetting ("product", ""));

        if ($processor > 0 And $m_level > 0)
        {
            $this->pageTitle = $dict['PM_pageTitle1'];
            $this->pageHeader = $dict['PM_pageTitle1'];
            $this->mainTemplate = "./templates/payment_f_form.tpl";

            $processor_title = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor'", "");
            $processor_fee = $this->db->GetOne ("Select fee From `processors` Where processor_id='$processor'", "");
            $level_title = $this->db->GetOne ("Select title From `types` Where order_index='$m_level'", "");
            $amount = $this->db->GetOne ("Select cost From `types` Where order_index='$m_level'", "");

            $full_sum_dig = $amount + $amount / 100 * $processor_fee;
            
            $full_sum_dig = sprintf ("%01.2f", $full_sum_dig);
            
            $full_sum = "<b>\$".$full_sum_dig."</b>";//"\$".$amount." + \$".$amount." / 100% * ".$processor_fee."% = <b>\$".$full_sum_dig."</b>";

            $product = $dict['PM_Payment_for_level'];

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                "PROCESSOR" => $processor_title,
                "PROCESSOR_FEE" => $processor_fee,
                "LEVEL" => $level_title,
                "AMOUNT" => $amount,//$full_sum_dig,
                "PRODUCT_NAME" => $product_name,
                "DETAILS" => $full_sum,
                "CODE" => getPayFormCode ($member_id, $full_sum_dig, $processor, $product, $m_level),
            );

        }
        elseif ($processor == -1 And $m_level > 0)
        {
            $amount = $this->db->GetOne ("Select cost From `types` Where order_index='$m_level'", "");
            $total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
            
            if ($total_cash >= $amount)
            {
                $this->db->ExecuteSql ("Insert Into `cash` (`amount`, `from_id`, `to_id`, `type_cash`, `descr`, `cash_date`, `payment_id`)
                                                    Values ('-$amount', '0', '$member_id', '0', '{$dict['PM_mess4']}', '".time ()."', '0')");
                payUpline ($member_id, time (), $m_level, '-2');
                $this->Redirect ("payment_res.php?res=ok");
            }
            else
            {
                $this->Redirect ($this->pageUrl."?res=noa");
            }
            
            
        }
        else
        {
            $this->Redirect ($this->pageUrl."?res=no");
        }
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("payment_f");

$zPage->Render ();

?>
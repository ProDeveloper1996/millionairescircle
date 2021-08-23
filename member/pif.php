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
        $this->mainTemplate = "./templates/pif.tpl";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->pageTitle = "Payment Page";
        $this->pageHeader = "Payment Page";
        $currency = $this->currency_synbol;
        $member_id = $this->member_id;
        $cycling = $this->db->GetSetting ("cycling", 1);
        
        $is_pif = $this->db->GetSetting ("is_pif");
        $status = getStatus ($member_id);

		if ($is_pif == 0 Or $status != "active") $this->Redirect ("./myaccount.php");
        $amount = 0;
        $prepayment = $this->GetGP ("prepayment", 0);

        if ($prepayment == 1)
        {
            $processor_id = $this->GetGP ("processor", 1);
            $this->SaveStateValue ("processor", $processor_id);
            
            $member = $this->GetGP ("member", 0);
            $this->SaveStateValue ("member", $member);
            
            $amount = $this->GetGP ("amount", 0);
            $this->SaveStateValue ("amount", $amount);
        }

        $processor_id = $this->GetStateValue ("processor", 1);
        $member = $this->GetStateValue ("member", 0);
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "AMOUNT" => $this->select_amount ($amount),
            "PROCESSOR" => $this->select_processor ($processor_id),
            "MEMBERS" => $this->select_members ($member),
        );

        if ($prepayment == 1)
        {
                
                
                 
                if ($member == 0) $this->Redirect ("./myaccount.php");
                
				if ($cycling == 1)
				{
                		$product_name = $this->dec ($this->db->GetSetting ("product", ""));
                		$description = $product = "Membership Fee";
                		$note = "Membership Fee for member ID #".$member;
                		$level = 1;
                }
                else
                {
                		$product_name = $this->dec ($this->db->GetSetting ("product", ""));
                		$description = $product = "Payment_for_level";
                		$note = "Payment_for_level for member ID #".$member;
                		$level = $this->db->GetOne ("Select `order_index` From `types` Where `cost`='$amount'", 1);
                }
                
                $this->SaveStateValue ("processor", $processor_id);
                $processor_title = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", "Cash account balance");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where processor_id='$processor_id'", "0.00");
                $full_sum_dig = $amount + $amount / 100 * $processor_fee;
                $full_sum = "\$".$amount." + \$".$amount." / 100% * ".$processor_fee."% = <b>\$".$full_sum_dig."</b>";
                
                $full_sum_dig = sprintf ("%01.2f", $full_sum_dig);
                $whole_amount = $full_sum_dig;
                
                
                
                $row_member = $this->db->GetOne ("Select CONCAT('#', member_id, ' : ', username) From `members` Where `member_id`='$member'", "");
                
                $whole_amount = sprintf ("%01.2f", $whole_amount);
                $this->data ['PREPAYMENT'][] = array (
                        "ROW_ACTION" => "",
                        "ROW_AMOUNT" => $whole_amount,
                        "ROW_PRODUCT" => $description,
                        "ROW_FEE" => $processor_fee,
                        "ROW_MEMBER" => $row_member,
                        
                        "ROW_WHOLE" => $whole_amount,
                        
                        "ROW_FULL_SUM" => $full_sum,
                        "ROW_PRODUCT_NAME" => $product_name,
                        "ROW_CODE" => ($processor_id > 0)? getPayFormCode ($member, $full_sum_dig, $processor_id, $product, $level, $note) :  $this->getCashButton ($member, $level),
                        "ROW_PROCESSOR" => $processor_title,
                    );
        }

    }
    
    //--------------------------------------------------------------------------
    function select_amount ($value = 0)
    {
 			$currency=$this->currency_synbol;
		  $cycling = $this->db->GetSetting ("cycling", 1);
        if ($cycling == 0)
        {
        		$toRet = "<select name='amount'> \r\n";
        		$result = $this->db->ExecuteSql ("Select * From `types` Where 1 And cost>0 Order By order_index");
        		while ($row = $this->db->FetchInArray ($result))
        		{
            			$order_index = $row['order_index'];
            			$cost = $row['cost'];
            
            			$selected = ($value == $cost)? "selected" : "";
            			$toRet .= "<option value='".$cost."' $selected>".$row['title']." (cost: $currency$cost)</option>";
        		}
        		$this->db->FreeSqlResult ($result);
        		return $toRet."</select>\r\n";
        }
        else
        {
				
				$amount = $this->db->GetOne ("Select `entrance_fee` From `matrixes` Where matrix_id=2");
				return $currency.$amount."<input type='hidden' name='amount' value='$amount' /><input type='hidden' name='level' value='1' />";
        }
        
    }
    
    //--------------------------------------------------------------------------
    function getCashButton ($member, $level)
    {
        $toRet = "<form action='' method='POST'>";
        $toRet .= "<input type='hidden' name='ocd' value='pay' />";
        $toRet .= "<input type='hidden' name='member' value='$member' />";
        $toRet .= "<input type='hidden' name='level' value='$level' />";
        $toRet .= "<input type='submit' value=' Pay Now ' class='some_btn' /></form>";
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function select_members ($member = 0)
    {
        
        $member_id = $this->member_id;
        $cycling = $this->db->GetSetting ("cycling", 1);
        $sql = ($cycling == 1)? " And `m_level`=0 " : "";
        
        $toRet = "<select name='member'> \r\n";
        $result = $this->db->ExecuteSql ("Select * From `members` Where `is_active`=1 And `is_dead`=0 And `member_id`!='$member_id' $sql Order By `member_id` Asc");
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['member_id'];
            $username = $row['username'];
            
            $enroller_id = $row['enroller_id'];
            $enroller_username = $this->db->GetOne ("Select `username` From `members` Where `member_id`='$enroller_id'", "System");
            
            $selected = ($id == $member)? "selected" : "";
            $toRet .= "<option value='".$id."' $selected>#".$id." : ".$username." sponsored by $enroller_username</option>";
        }
        $this->db->FreeSqlResult ($result);
        return $toRet."</select>\r\n";
    }
    
    //--------------------------------------------------------------------------
    function select_processor ($processor_id = 1)
    {
        $toRet = "<select name='processor'> \r\n";
        
        //from cash account
        $is_pif_cash = $this->db->GetSetting ("is_pif_cash");
        
        if ($is_pif_cash == 1)
        {
            $cycling = $this->db->GetSetting ("cycling", 1);
            $min_amount = ($cycling == 0)? $this->db->GetOne ("Select MIN(cost) From `types` Where cost>0") : $this->db->GetOne ("Select `entrance_fee` From `matrixes` Where matrix_id=2");
            $total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
            
            $total_cash = sprintf ("%01.2f", $total_cash);
            
            if ($total_cash >= $min_amount)
            $toRet .= "<option value='-1'>From my account cash balance (balance: $".$total_cash.")</option>";
        }
        
        $result = $this->db->ExecuteSql ("Select * From `processors` Where is_active=1 Order By name");
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['processor_id'];
            $fee = $row['fee'];
            $selected = ($id == $processor_id)? "selected" : "";
            $toRet .= "<option value='".$id."' $selected>".$row['name']." (fee: $fee%)</option>";
        }
        $this->db->FreeSqlResult ($result);
        return $toRet."</select>\r\n";
    }
    
    //--------------------------------------------------------------------------
    function ocd_pay ()
    {
        $member = $this->GetID ("member", 0);
        $level = $this->GetID ("level", 1);
        $cycling = $this->db->GetSetting ("cycling", 1);
        $is_pif = $this->db->GetSetting ("is_pif");
        $is_pif_cash = $this->db->GetSetting ("is_pif_cash");
        
        $total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
        $amount = ($cycling == 0)? $this->db->GetOne ("Select MIN(cost) From `types` Where `order_index`='$level'") : $this->db->GetOne ("Select `entrance_fee` From `matrixes` Where matrix_id=2");
        
        $full_amount = $amount;
        
        if ($full_amount <= $total_cash And $is_pif == 1 And $is_pif_cash == 1)
        {
            $this->db->ExecuteSql ("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$full_amount', '1', '0', '".$this->member_id."', '".time ()."', 'PIF payment for #$member', '0')");
            payUpline ($member, time (), $level, -1);
            $this->Redirect ("payment_res.php?res=ok");
        }
        $this->Redirect ($this->pageUrl);         
        
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("account");

$zPage->Render ();

?>
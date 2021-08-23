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
        $this->mainTemplate = "./templates/payment.tpl";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['PM_pageTitle'];
        $this->pageHeader = $dict['PM_pageTitle'];
        $member_id = $this->member_id;

        $cycling = $this->db->GetSetting ("cycling", 1);

        if ($cycling == 0) $this->Redirect ("payment_f.php");
        $prepayment = $this->GetGP ("prepayment", 0);

        if ($prepayment == 1)
        {
            $processor_id = $this->GetGP ("processor", 1);
            $this->SaveStateValue ("processor", $processor_id);
        }
        switch ($cycling)
        {
            case 1:
                $amount = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
                $level = 1;
            break;
            case 0:
            break;
            default:
                $amount = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id=2");
        }

        $processor_id = $this->GetStateValue ("processor", 1);
        $prepayment = $this->GetGP ("prepayment", 0);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "AMOUNT" => $amount,
            "PROCESSOR" => $this->select_processor ($processor_id),
        );

        if ($prepayment == 1)
        {
            $m_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$member_id'", 0);
            if ($m_level == 0)
            {
                $product_name = $this->dec ($this->db->GetSetting ("product", ""));
                $description = $dict['PM_MembershipFee'];
                $product = $dict['PM_MembershipFee'];
                $this->SaveStateValue ("processor", $processor_id);
                $processor_title = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", "");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where processor_id='$processor_id'", "0.00");
                $full_sum_dig = $amount + $amount / 100 * $processor_fee;
                
                $full_sum_dig = sprintf ("%01.2f", $full_sum_dig);
                
                $full_sum = $full_sum_dig;
                $this->data ['PREPAYMENT'][] = array (
                        "ROW_ACTION" => "",
                        "ROW_AMOUNT" => $amount,
                        "ROW_PRODUCT" => $description,
                        "ROW_FEE" => $processor_fee,
                        "ROW_FULL_SUM" => $full_sum,
                        "ROW_PRODUCT_NAME" => $product_name,
                        "ROW_CODE" => getPayFormCode ($member_id, $full_sum_dig, $processor_id, $product, $level),
                        "ROW_PROCESSOR" => $processor_title,
                    );
            }
            else
            {
                $this->data ['PREPAYMENT_NO'][] = array (
                        "NO_RIGHT" => "No need to pay more"
                        );
            }
        }

    }

    //--------------------------------------------------------------------------
    function select_processor ($processor_id = 1)
    {
        $toRet = "<select name='processor' onChange='this.form.submit();' class='form-control' > \r\n";
        $toRet .= "<option value='' >Choose wallet</option>";
        $result = $this->db->ExecuteSql ("Select * From `processors` Where is_active=1 Order By name");
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['processor_id'];
            $fee = $row['fee'];
            $selected = ($id == $processor_id)? "selected" : "";
            $toRet .= "<option value='".$id."' $selected>".$row['name']." (fee: $fee%)</option>";
        }
        return $toRet."</select>\r\n";
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("account");

$zPage->Render ();

?>
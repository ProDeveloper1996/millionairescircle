<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
/*
    var $statusList = array(
	 	'ru'=>array ("0" => "Оплачено", "1" => "Завершена", "2" => "Отклонено"),
	 	'en'=>array ("0" => "Pending", "1" => "Completed", "2" => "Declined"),
	 ); 
 */
	 

    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        global $dict ;
       $this->orderDefault = "transfer_date";
        XPage::XPage ($object);
        $this->statusList=$dict['statusList'];
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        global $cashoutStatusArray, $dict ;
        $this->mainTemplate = "./templates/withdrawal.tpl";
        $this->pageTitle = $dict['WD_pageTitle'];
        $this->pageHeader = $dict['WD_pageTitle'];
        $fee = $this->db->GetSetting("fee");
        $filter2 = $this->GetGP ("filter2");
        if ($filter2 == 1)
        {
            $status = $this->enc ($this->GetID ("status"));
            $this->SaveStateValue ("status", $status);
        }
        $member_id = $this->member_id;
        $status = $this->GetStateValue ("status");
        $sql_select = "";
        if ($status < 3) $sql_select .= "And status='$status'";
        $select_status = $this->getStatusSelect ($status);

        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where 1 $sql_select and member_id='$member_id'");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", $dict['WD_Amount']),
            "HEAD_DATE" => $this->Header_GetSortLink ("transfer_date", $dict['WD_Date']),
            "HEAD_STATUS" => $this->Header_GetSortLink ("status", $dict['WD_Status']),
            "HEAD_PROCESSOR" => $this->Header_GetSortLink ("processor", $dict['WD_Processor']),
            "HEAD_ACCOUNT_ID" => $this->Header_GetSortLink ("account_id", $dict['WD_AccountID']),
            "HEAD_FEE" => $dict['WD_FEE'],
            "STATUS" => $select_status,
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where 1 $sql_select And member_id='$member_id' Order By {$this->orderBy} {$this->orderDir}", true);

            $WITHDRAWAL_VALUE = $this->db->GetSetting("WITHDRAWAL_VALUE" );
            if ( $WITHDRAWAL_VALUE == 2 ) $WITHDRAWAL_VALUE1 = '%';
            else $WITHDRAWAL_VALUE1 = $this->currency_synbol;

            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['cash_out_id'];
                $amount = $row['amount'];
                $processor = $row['processor'];
                $account_id = $row['account_id'];
                $processor = $this->db->GetOne ("Select name From `processors` Where processor_id=$processor");
                $fees = $WITHDRAWAL_VALUE1.$fee;
                $pay_cash_out = "&nbsp;";
                $date = date ("d M Y H:i", $row['transfer_date']);
                $status = $row['status'];
                $status = $this->statusList[$status];
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_AMOUNT" => $amount,
                    "ROW_FEE" => $fees,
                    "ROW_DATE" => $date,
                    "ROW_STATUS" => $status,
                    "ROW_PROCESSOR" => $processor,
                    "ROW_ACCOUNT_ID" => $account_id,
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
    function getStatusSelect ($value = "")
    {
        $toRet = "<select name='status' style='width:120px;' maxlength='10' onChange='this.form.submit()';>";
        foreach ($this->statusList as $k => $v)
            $toRet .= ($value == $k) ? "<option value='$k' selected>$v</option>" : "<option value='$k'>$v</option>";
        $toRet .= "</select>";
        return $toRet;
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cash_out");

$zPage->Render ();

?>
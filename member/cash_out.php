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
        $this->mainTemplate = "./templates/cash_out.tpl";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
		  GLOBAL $dict;
        $this->pageTitle = $dict['CashO_pageTitle'];
        $this->pageHeader = $dict['CashO_pageTitle'];
        $currency = $this->currency_synbol;
        $mes = ($this->GetGP ("ec") == "yes")? $dict['CashO_Text1'] : "";
        $total_cash =  $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
        $cash = "<input type='text' name='cash' value='$total_cash' style='width:50px;'>";
        $fee = $this->db->GetSetting ("fee", "0.00");
        $WITHDRAWAL_VALUE = $this->db->GetSetting("WITHDRAWAL_VALUE" );
        if ( $WITHDRAWAL_VALUE == 2 ) $WITHDRAWAL_VALUE = '%';
        else $WITHDRAWAL_VALUE = $currency;

        $selectProcessor = $this->getProcessor (0);
        $min_cash_out = $this->db->GetSetting ("MinCashOut", "0.00");

        $processor = $this->db->GetOne ("Select processor From `members` Where member_id='{$this->member_id}'", 0);
        $account_id = $this->db->GetOne ("Select account_id From `members` Where member_id='{$this->member_id}'", "");

        $btn = '<button type="submit" class="btn btn-form"><i class="fa fa-check"></i> '.$dict['CashO_Process'].'</button> <button type="button" class="btn btn-form-cancel" onClick="window.location.href=\'cash.php\'">'.$dict['CashO_Cancel'].'</button> ';   

        $but = ($total_cash > 0)? $btn : $dict['CashO_NoCash'];
        $but = ($processor > 0 && $account_id!='')? $btn : 'Please fulfill the field with your <a href="./myaccount.php?paymentsettings">Processor data</a> to be able to withdraw funds.';

        $this->data = array (
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $mes,
            "MAIN_CASH" => $total_cash,
            "CASH" => $cash,
            "MIN_CASH_OUT" => $min_cash_out,
            "PROCESSOR" => $selectProcessor,
            "FEE" => $fee,
            "BUTTON" => $but,
            'WITHDRAWAL_VALUE' => $WITHDRAWAL_VALUE
        );
    }

    //--------------------------------------------------------------------------
    function ocd_cash_out ()
    {
		  GLOBAL $dict;

        $this->pageTitle = $dict['CashO_pageTitle'];
        $this->pageHeader = $dict['CashO_pageTitle'];
        $cash = $this->GetValidGP ("cash", $dict['CashO_error1'], VALIDATE_NUMERIC_POSITIVE);
        $total_cash =  $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
        $processor = $this->GetID ("processor");
        $fee = $this->db->GetSetting ("fee", "0.00");
        $currency = $this->currency_synbol;
        $WITHDRAWAL_VALUE = $this->db->GetSetting("WITHDRAWAL_VALUE" );
        if ( $WITHDRAWAL_VALUE == 2 ) $WITHDRAWAL_VALUE1 = '%';
        else $WITHDRAWAL_VALUE1 = $currency;

        if ($cash > $total_cash)
        {
           $this->SetError ("cash", $dict['CashO_error2']);
        }

        $min_cash_out = $this->db->GetSetting ("MinCashOut", "0.00");

        if ($cash < $min_cash_out)
        {
           $this->SetError ("cash", $dict['CashO_error3']." $currency$min_cash_out. ".$dict['CashO_error4']);
        }

        if ($this->errors['err_count'] > 0)
        {
            $cash = "<input type='text' name='cash' value='$total_cash' style='width:50px;'>";
            $btn = '<button type="submit" class="btn btn-form"><i class="fa fa-check"></i> '.$dict['CashO_Process'].'</button><button type="button" class="btn btn-form-cancel" onClick="window.location.href=\'cash.php\'">'.$dict['CashO_Cancel'].'</button> ';   
            $selectProcessor = $this->getProcessor ($processor);
            $this->data = array (
                "MAIN_ACTION" => $this->pageUrl,
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CASH" => $total_cash,
                "CASH" => $cash,
                "CASH_ERROR" => $this->GetError ("cash"),
                "MIN_CASH_OUT" => $min_cash_out,
                "PROCESSOR" => $selectProcessor,
                "FEE" => $fee,
                'WITHDRAWAL_VALUE' => $WITHDRAWAL_VALUE1,
                "BUTTON" => ($total_cash > 0)? $btn : $dict['CashO_NoCash'],
            );
        }
        else
        {

            $account_id = $this->db->GetOne ("Select account_id From `members` Where member_id='{$this->member_id}'", "");

            $this->db->ExecuteSql ("Insert Into `cash_out` (member_id, processor, account_id, transfer_date, amount, status) Values ('{$this->member_id}', '$processor', '$account_id','".time()."', $cash, 0)");
            $this->db->ExecuteSql("Insert into `cash` (type_cash, to_id, from_id, amount, descr, cash_date) values (-1, '{$this->member_id}', 0, -$cash, 'Cashout Request', '".time()."')");

            $row = $this->db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='19'", "");
            if ($row ["is_active"] == 1) {
                $SiteTitle = $this->db->GetSetting("SiteTitle");
                $adminEmail = $this->db->GetSetting ("ContactEmail");
                $subject = $this->dec($row ["subject"]);
                $message = $this->dec($row ["message"]);
                $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);
                $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                sendMail($adminEmail, $subject, $message, $this->emailHeader);

            }

            $this->Redirect ("cash.php");
        }
    }

    //--------------------------------------------------------------------------
    function getProcessor ($value = 0)
    {
        $processor = $this->db->GetOne ("Select processor From `members` Where member_id='{$this->member_id}'", 0);
        $account_id = $this->db->GetOne ("Select account_id From `members` Where member_id='{$this->member_id}'", "");

        $toRet = "<select name='processor' style='width:100px;'> \r\n";

        $allow_another_processor = $this->db->GetOne ("Select is_active From `processors` Where processor_id='$processor'", 0);

        if ($allow_another_processor == 1 And $account_id != "")
        {
            $selected = ($value == $processor)? "selected" : "";

            $name = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor'", "");

            $toRet .= "<option value='".$processor."' $selected>".$name."</option>";
        }

        return $toRet."</select>\r\n";
    }

}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cash_");

$zPage->Render ();

?>
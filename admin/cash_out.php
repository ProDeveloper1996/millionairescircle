<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    var $statusList = array ("3" => "All", "0" => "Pending", "1" => "Completed", "2" => "Declined");

    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->orderDefault = "transfer_date";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        global $cashoutStatusArray ;
        $this->mainTemplate = "./templates/cash_out.tpl";
        $this->pageTitle = "Withdrawal requests";
        $this->pageHeader = "Withdrawal requests";
        $fee = $this->db->GetSetting("fee");
        $filter1 = $this->GetGP ("filter1");
        $filter2 = $this->GetGP ("filter2");
        if ($filter1 == 1)
        {
            $member_id = $this->enc ($this->GetGP ("member_id"));
            $this->SaveStateValue ("member_id", $member_id);
        }
        if ($filter2 == 1)
        {
            $status = $this->enc ($this->GetGP ("status"));
            $this->SaveStateValue ("status", $status);
        }
        $member_id = $this->GetStateValue ("member_id");
        $status = $this->GetStateValue ("status");
        $sql_select = "";

        if ($status < 3) $sql_select .= "And status='$status'";
        if ($member_id != "") $sql_select .= " And member_id='$member_id'";
        $select_status = $this->getStatusSelect ($status);

        $totalAmount = $this->db->GetOne ("Select Sum(amount) From `{$this->object}` Where 1 $sql_select", 0);
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where 1 $sql_select");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_DELETEALLDENIED" => "<input type='button' class='some_btn' value='Delete all declined Withdrawals' onClick=\"if (confirm ('Are you sure?')) {window.location.href='{$this->pageUrl}?ocd=delalldenied';}\">",
            "HEAD_USERNAME" => $this->Header_GetSortLink ("member_id", "Member (ID)"),
            "HEAD_AMOUNT" => $this->Header_GetSortLink ("amount", "Amount"),
            "HEAD_DATE" => $this->Header_GetSortLink ("transfer_date", "Date"),
            "HEAD_PROCESSOR" => $this->Header_GetSortLink ("processor", "Processor"),
            "HEAD_FEE" => "Fee",
            "HEAD_PAY" => "Make Withdrawal",
            "STATUS" => $select_status,
            "MAIN_FILTER" => "<input type='text' name='member_id' value='$member_id' style='width:60px;' maxlength='6'>",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $stat_url = $this->db->GetSetting ("SiteUrl")."notify/egold.php";
            $e_gold_url = $this->db->GetOne ("Select routine_url From `processor` Where keyname='egold'", "");
            $pay_url = $this->db->GetSetting ("SiteUrl")."admin/cash_out.php";
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where 1 $sql_select Order By {$this->orderBy} {$this->orderDir}", true);

            $thisTime = time ();
            $turn_date = $this->db->GetSetting ("PaymentModeDate");
            $monthPeriod = $this->db->GetSetting ("monthPeriod");
            $payPeriod = $this->db->GetSetting ("payPeriod");

            $WITHDRAWAL_VALUE = $this->db->GetSetting("WITHDRAWAL_VALUE" );
            if ( $WITHDRAWAL_VALUE == 2 ) $WITHDRAWAL_VALUE1 = '%';
            else $WITHDRAWAL_VALUE1 = $this->currency_synbol;
            
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['cash_out_id'];
                $member_id = $row['member_id'];
                $name = $this->db->GetOne ("Select CONCAT(first_name, ' ',last_name) From `members` Where member_id='$member_id'", "");
                $user_name = $name . " ($member_id)";
                $amount = $row['amount'];

                $processor_id = $row['processor'];
                $processor = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", "");
                $account_id = $this->dec ($row['account_id']);
                $title_status = "&nbsp;";
                $with_fee = $amount - $fee;
                if ( $WITHDRAWAL_VALUE == 2 ) {
                    $amount = $this->db->GetOne ("Select amount From `cash_out` Where cash_out_id=$id");
                    $with_fee = $amount - $fee;
                }

                $fee_list = ($row['status'] > 0)? "&nbsp;" : $WITHDRAWAL_VALUE1.$fee;
                $date = date ("d M Y H:i", $row['transfer_date']);
                
                if ($row['status'] == 0)
                {
                    
                    
                    $declineLink = "<a href='{$this->pageUrl}?ocd=decline&id=$id' onClick=\"return confirm ('Do you really want to decline this request?')\"><img src='./images/decline.png' border='0' title='Decline the withdrawal' alt='Decline the withdrawal'></a><br>";
                    $completeLink = "<a href='{$this->pageUrl}?ocd=complete&id=$id' onClick=\"return confirm ('Do you really want to complete this request?')\"><img src='./images/money.png' border='0' title='Complete the withdrawal' alt='Complete the withdrawal'></a> ";
                    $pay_cash_out = getPayFormCodeAdmin ($id, $member_id, $with_fee, $processor_id, $account_id);
                }
                else
                {
                    $completeLink = "&nbsp;";
                    $declineLink = "&nbsp;";
                    $pay_cash_out = ($row['status'] == 1)? "Completed" : "Declined";
                }

                if ($row['status'] == 1) $title_status = "Completed";
                if ($row['status'] == 2) $title_status = "Declined";
                if ($row['status'] == 0) $title_status = "Pending";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_USERNAME" => "<a href='{$this->pageUrl}?ocd=tomem&id=$member_id' target='_blank'>".$user_name."</a>",
                    "ROW_AMOUNT" => $amount,
                    "ROW_FEE" => $fee_list,
                    "ROW_DATE" => $date,
                    "ROW_PAY" => $pay_cash_out,                   
                    "ROW_COMPLETE" => $completeLink,
                    "ROW_DECLINE" => $declineLink,
                    "ROW_PROCESSOR" => $processor." (".$account_id.")",
                    
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
    function ocd_decline ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Update `{$this->object}` Set status=2 Where cash_out_id='$id'");
        $member_id = $this->db->GetOne ("Select member_id From `{$this->object}` Where cash_out_id='$id'");
        $amount = $this->db->GetOne ("Select amount From `{$this->object}` Where cash_out_id='$id'");
        $this->db->ExecuteSql ("Insert into cash (amount, type_cash, to_id, cash_date, descr) values ('$amount', 2, '$member_id', '".time()."', 'Declined Cash Out Request')");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_delalldenied ()
    {
        $this->db->ExecuteSql ("Delete From `{$this->object}` Where status=2");
        $this->Redirect ($this->pageUrl);
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

    //--------------------------------------------------------------------------
    function ocd_complete ()
    {
        $id = $this->GetGP ("id");
        $fee = $this->db->GetSetting("fee");
        $WITHDRAWAL_VALUE = $this->db->GetSetting("WITHDRAWAL_VALUE" );
        if ( $WITHDRAWAL_VALUE == 2 ) {
            $amount = $this->db->GetOne ("Select amount From `cash_out` Where cash_out_id=$id");
            $fee = ($fee/100)*$amount;
        }
        $SiteTitle = $this->db->GetSetting ("SiteTitle");

        $this->db->ExecuteSql ("Update `cash_out` Set status=1, amount=amount-'$fee' Where cash_out_id=$id");
        $amount = $this->db->GetOne ("Select amount From `cash_out` Where cash_out_id=$id");
        $member_id = $this->db->GetOne ("Select member_id From `cash_out` Where cash_out_id='$id'", 0);
        
        //email notification
        $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='16'", "");
            
        if ($row ["is_active"] == 1 and $member_id > 0)
        {
            $first_name = $this->dec ($this->db->GetOne ("Select first_name From `members` Where member_id='$member_id'"));
            $last_name = $this->dec ($this->db->GetOne ("Select last_name From `members` Where member_id='$member_id'"));
            $email = $this->db->GetOne ("Select email From `members` Where member_id='$member_id'");
            $username = $this->db->GetOne ("Select username From `members` Where member_id='$member_id'");
                
            $subject = $this->dec ($row ["subject"]);
            $message = $this->dec ($row ["message"]);
            $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
            $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
            $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
            $message = preg_replace ("/\[LastName\]/", $last_name, $message);
            $message = preg_replace ("/\[Username\]/", $username, $message);
            $message = preg_replace ("/\[Email\]/", $email, $message);
            $message = preg_replace ("/\[Amount\]/", $amount, $message);
            sendMail ($email, $subject, $message, $this->emailHeader);
            
        }
        $this->Redirect ($this->pageUrl);
    }

//------------------------------------------------------------------------------    
function ocd_tomem ()
    {
        $id = $this->GetGP ("id", 0);
        $_SESSION['MemberID'] = $id;
        
        $this->Redirect ("../member/cash.php");
        
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("cash_out");

$zPage->Render ();

?>
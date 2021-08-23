<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    var $searchList = array ("Select the parameter..." => "", "Member ID" => "member_id", "Username" => "username", "Email" => "email", "First Name" => "first_name", "Last Name" => "last_name", "Address" => "street", "Address" => "street", "City" => "city", "State" => "state", "Country" => "country", "Postal Code" => "postal", "Phone" => "phone", "Referrer ID" => "enroller_id", "Level" => "m_level");
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->orderDefault = "member_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $currency = $this->currency_synbol;
        $m = $this->GetGP ("m", "");
        $iss_replica = $this->db->GetSetting ("is_replica");
        $this->javaScripts = $this->GetJavaScript ();
        $mess = "";
        if ($m == "yes") $mess = "The payment was registered";
        if ($m == "no") $mess = "Cannot register this payment";

        $this->mainTemplate = "./templates/members.tpl";
        $this->pageTitle = "Members List";
        $this->pageHeader = "Members List";
        
        $filter = $this->GetGP ("filter", 0);

        $cycling = $this->db->GetSetting ("cycling", 0);

        if ($filter == 1)
        {
            $s_line = $this->enc ($this->GetGP ("s_line", ""));
            $s_field = $this->enc ($this->GetGP ("s_field", ""));

            $filterDateDayBegin = $this->GetGP ("filterDateDayBegin");
            $filterDateMonthBegin = $this->GetGP ("filterDateMonthBegin");
            $filterDateYearBegin = $this->GetGP ("filterDateYearBegin");
            $filterDateDayEnd = $this->GetGP ("filterDateDayEnd");
            $filterDateMonthEnd = $this->GetGP ("filterDateMonthEnd");
            $filterDateYearEnd = $this->GetGP ("filterDateYearEnd");

            $this->SaveStateValue ("filterDateBegin", mktime(0, 0, 0, $filterDateMonthBegin, $filterDateDayBegin, $filterDateYearBegin));
            $this->SaveStateValue ("filterDateEnd", mktime(23, 59, 59, $filterDateMonthEnd, $filterDateDayEnd, $filterDateYearEnd));

            $this->SaveStateValue ("s_line", $s_line);
            $this->SaveStateValue ("s_field", $s_field);
        }

        $main_assign = "";
        $sql_select = "";
        $sel_pay = "";
        $sel_act = "";
        $sel_all = "";
        $sel_unpaid = "";
        $filterDateBegin = $this->GetStateValue ("filterDateBegin", 0);
        $filterDateEnd = $this->GetStateValue ("filterDateEnd", 0);

        $s_field = $this->GetStateValue ("s_field", "");
        $s_line = $this->GetStateValue ("s_line", "");

        if ($s_field != "" And $s_line != "")
        {
            $sql_select .= " And $s_field='$s_line'";
        }

        $reg_date = $this->db->GetOne("Select min(reg_date) From {$this->object} ", 0);

        $filterDateDayBegin = ($filterDateBegin != 0) ? date ("d", $filterDateBegin) : date('d', $reg_date);
        $filterDateMonthBegin = ($filterDateBegin != 0) ? date ("m", $filterDateBegin) : date('m', $reg_date);
        $filterDateYearBegin = ($filterDateBegin != 0) ? date ("Y", $filterDateBegin) : date ("Y", $reg_date);
        $filterDateDayEnd = ($filterDateEnd != 0) ? date ("d", $filterDateEnd) : "";
        $filterDateMonthEnd = ($filterDateEnd != 0) ? date ("m", $filterDateEnd) : "";
        $filterDateYearEnd = ($filterDateEnd != 0) ? date ("Y", $filterDateEnd) : "";

        if ($filterDateBegin != 0) $sql_select .= " And reg_date>$filterDateBegin And reg_date<$filterDateEnd";

        $filter = "";
        $filter .= "Registered From ";
        $filter .= getDaySelect ($filterDateDayBegin, "filterDateDayBegin");
        $filter .= getMonthSelect ($filterDateMonthBegin, "filterDateMonthBegin");
        $filter .= getYearSelect ($filterDateYearBegin, "filterDateYearBegin");
        $filter .= " to ";
        $filter .= getDaySelect ($filterDateDayEnd, "filterDateDayEnd");
        $filter .= getMonthSelect ($filterDateMonthEnd, "filterDateMonthEnd");
        $filter .= getYearSelect ($filterDateYearEnd, "filterDateYearEnd");
        $main_assign = "<input type='submit' value='Build matrix'>";
        $ref_zero = "<input type='submit' value='Clear Matrix'>";
        $main_clear = "<input type='submit' value='Clear members DB and Matrix'>";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where 1 $sql_select", 0);
        $this->data = array (
            "MAIN_ASSIGN" => $main_assign,
            "MAIN_MESSAGE" => $mess,
            "REF_ZERO" => $ref_zero,
            "MAIN_CLEAR" => $main_clear,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' alt='Add New Member' title='Add New Member'><img src='./images/add_member.png'></a>",
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_FILTER" => $filter,

            "SEARCH_LIST" => $this->selectSearch ($s_field),
            "SEARCH_LINE" => "<input type='text' name='s_line' value='$s_line' maxlength='80'>",
            "HEAD_MEMBER_ID" => $this->Header_GetSortLink ("member_id", "ID"),
            "HEAD_USERNAME" => $this->Header_GetSortLink ("username", "Username"),
            "HEAD_FIRST_NAME" => $this->Header_GetSortLink ("first_name", "First name"),
            "HEAD_LAST_NAME" => $this->Header_GetSortLink ("last_name", "Last name"),
            "HEAD_REG_DATE" => $this->Header_GetSortLink ("reg_date", "Registered"),
            "HEAD_EMAIL" => $this->Header_GetSortLink ("email", "E-mail"),
            "HEAD_SPONSOR" => $this->Header_GetSortLink ("enroller_id", "Referrer ID"),
            "HEAD_EARNINGS" => $this->Header_GetSortLink ("all_cash", "Earnings"),
            "HEAD_LEVEL" => $this->Header_GetSortLink ("m_level", "Level"),
            "HEAD_REFERRER" => "Referrer",
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select a. * , sum( b.amount ) all_cash FROM members a LEFT JOIN cash b ON a.member_id = b.to_id Where 1 $sql_select GROUP BY a.member_id Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))

            {
                $member_id = $row['member_id'];
                $username = $this->dec ($row['username']);
                $firstname = $this->dec ($row['first_name']);
                $lastname = $this->dec ($row['last_name']);
                $earnings = $row['all_cash'];
                if ($earnings == "") $earnings = "0.00";
                $email = $this->dec ($row['email']);
                $level = $row['m_level'];
                $level = ($level > 0)? $this->dec ($this->db->GetOne ("Select title From `types` Where order_index='$level'", "&nbsp;")) : "Unpaid";
                $sponsor_id = $row['enroller_id'];
                
                $inoutCash = "<a href='{$this->pageUrl}?ocd=inout&id=$member_id'><img src='./images/plus_minus.gif' border='0' title='Add/Susbtract Commissions' /></a>";
                
                $inoutLink = "&nbsp;";
                
                $replicaLink = ($iss_replica == 1)? "<a href='replica_site.php?id=$member_id' title='Replicated Site Pages'><img src='./images/replica_icon.png' border='0' alt='Replica Site Constructor' title='Replica Site Constructor' /></a>" : "&nbsp;";
                if (isset($this->LicenseAccess['is_replica']) && $this->LicenseAccess['is_replica'][$this->lic_key]==0)  $replicaLink = '';

                if ($cycling == 1)
                {
                    $payins = $this->db->GetOne ("Select Count(*) From `payins` Where member_id='$member_id'", 0);
                    $payLink = ($payins == 0)? "<a href='{$this->pageUrl}?ocd=pay&id=$member_id' onClick=\"return confirm ('Do you really want to pay for this member?');\"><img src='./images/money.png' border='0' title='Make payment and place member in matrix' /></a>" : "&nbsp";
                    
                    $is_there = $this->db->GetOne ("Select Count(*) From `places` Where member_id='$member_id'", 0);
                    $referrer_id = ($is_there > 0)? "In matrix" : "Out of matrix";
                    
                }
                else
                {
                    $referrer_id = $this->db->GetOne ("Select referrer_id From `matrix` Where member_id=$member_id", 0);
                    $count_in_m = $this->db->GetOne ("Select Count(*) From `matrix` Where member_id='$member_id'", 0);
                    $payLink = ($count_in_m == 0)? "<a href='{$this->pageUrl}?ocd=pay&id=$member_id' onClick=\"return confirm ('Do you really want to pay for the member and place them to the matrix?');\"><img src='./images/money.png' border='0' title='Place member into the matrix' /></a>" : "<a href='{$this->pageUrl}?ocd=out_matrix&id=$member_id' onClick=\"return confirm ('Do you really want to get this member out of the matrix?');\"><img src='./images/money_no.png' border='0' title='Get him out of the matrix' /></a>";
                }
                
                
                
                
                if ($sponsor_id == 0) $sponsor_id = "System";
                if ($member_id == 1)
                {
                    $referrer_id = "&nbsp;";
                    $sponsor_id = "&nbsp;";
                }
/*                
                if ($referrer_id == 0 And $member_id > 1)
                {
                    $inoutLink = "<a href='{$this->pageUrl}?ocd=inmatrix&id=$member_id'><img src='./images/in_matrix.gif' border='0' alt='Put in matrix'></a>";
                    $referrer_id = "Out of matrix";
                }
                if ($referrer_id > 0)
                {
                    $inoutLink = "<a href='{$this->pageUrl}?ocd=outmatrix&id=$member_id' onClick=\"return confirm ('Do you really want to get this member out of the matrix?');\"><img src='./images/out_matrix.gif' border='0' alt='Out of matrix'></a>";
                }
*/
                $reg_date = date ("d-M-Y H:i", $row['reg_date']);
                $dReg = $row['reg_date'];

                $matrixLink = ($cycling == 1)? "<a target='blank' href='memb_matrix.php?id=$member_id'><img src='./images/viewmatrix.png' border='0' title='View matrix' /></a>" : "<a target='blank' href='m_levels.php?c_m_id=$member_id'><img src='./images/viewmatrix.png' border='0' title='View matrix' /></a>";

                $activeLink = ($member_id > 1)? "<a href='javascript:is_active(\"".$this->object."\", \"member_id\", ".$member_id.")'><img src='./images/active".$row['is_active'].".png' border='0' title='Change activity status' /></a>" : "&nbsp";
                $isreplicaLink ="<a href='javascript:is_replica(\"".$this->object."\", \"member_id\", ".$member_id.")'><img src='./images/replica".$row['is_a_replica'].".png' border='0' title='Change replicated site activity status' /></a>";
                
                $ipLink = ($row['ip_check'] == 1)? "<a href='javascript:ip_check(\"".$this->object."\", \"member_id\", ".$member_id.")'><img src='./images/verify1.png' border='0' alt='Remove IP protection' title='Remove IP protection' /></a>" : "&nbsp";

                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$member_id'><img src='./images/account_details.png' border='0' alt='Edit members fields' title='View member's details and edit members fields' /></a>";

                $delLink = ($member_id > 1)? "<a href='{$this->pageUrl}?ocd=del&id=$member_id' onClick=\"return confirm ('Do you really want to change the deletion status?');\"><img src='./images/dead".$row['is_dead'].".png'  border='0' alt='Change the deletion status' title='Change the deletion status' /></a>" : "&nbsp";
				$delForever = ($member_id > 1)? "<a href='{$this->pageUrl}?ocd=delforever&id=$member_id' onClick=\"return confirm ('Do you really want to remove this member out of the system?');\"><img src='./images/trash.png'  border='0' alt='Completely remove member out of the system' title='Completely remove member out of the system' /></a>" : "&nbsp";
                

                $passLink = "<a href='{$this->pageUrl}?ocd=pass&id=$member_id' onClick=\"return confirm ('Do you really want to change the password and send it to this member?');\"><img src='./images/key_icon.png' border='0' alt='Generate and Send New Password' title='Generate and Send New Password' /></a>";

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_MEMBER_ID" => $member_id,
                    "ROW_FIRST_NAME" => $firstname,
                    "ROW_LAST_NAME" => $lastname,
                    "ROW_EMAIL" => "<a href=mailto:".$email.">".$email."</a>",
                    "ROW_REG_DATE" => $reg_date,
                    "ROW_SPONSOR" => $sponsor_id,
                    "ROW_EARNINGS" => $earnings,
                    "ROW_LEVEL" => $level,
                    "ROW_INOUT" => $inoutLink,
                    "ROW_INOUTCASH" => $inoutCash,
                    "ROW_MATRIX" => $matrixLink,
                    "ROW_ACTIVELINK" => "<div id='resultik$member_id'>".$activeLink."</div>",
                    "ROW_EDITLINK" => $editLink,
                    "ROW_IPLINK" => "<div id='resultika$member_id'>".$ipLink."</div>",
                    "ROW_DELLINK" => $delLink,
                    "ROW_DELFOREVER" => $delForever,
                    "ROW_PASSLINK" => $passLink,
                    "ROW_PAYLINK" => ($member_id > 1)? $payLink : "",
                    "ROW_USERNAME" => "<a href='{$this->pageUrl}?ocd=tomem&id=$member_id' target='_blank'>".$username."</a>",
                    "ROW_REPLICALINK" => $replicaLink,
                    "ROW_ISREPLICALINK" => (isset($this->LicenseAccess['is_replica']) && $this->LicenseAccess['is_replica'][$this->lic_key]==0?'':"<div id='resultikb$member_id'>".$isreplicaLink."</div>")  ,
                    
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

    //------------------------------------------------------------------------------
    function ocd_inout ()
    {
        $member_id = $this->GetGP ("id", 0);
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        $this->pageTitle = "Add / Substract Commissions";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Members List</a> / Add/Substract commissions of ".$name." (id=".$member_id.")";
        $this->mainTemplate = "./templates/subtract.tpl";
        $balance = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='$member_id'", "0.00");

        $mess = "";
        $res = $this->GetGP ("res", "");
        if ($res == "nod") $mess = "Please fill in all fields properly";
        if ($res == "nom") $mess = "This member was not found in database";
        if ($res == "ok") $mess = "Operation was successfully completed";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "BALANCE" => $balance,
            "MAIN_MESS" => $mess,
            "ID" => $member_id,
            "AMOUNT" => "<input type='text' name='amount' value='' maxlength='7' style='width: 100px;'>",
            "DESCRIPTION" => "<input type='text' name='description' value='' maxlength='50' style='width: 95%;'>",
            );
    }
    
    //------------------------------------------------------------------------------
    function ocd_substract ()
    {
        $member_id = $this->GetGP ("id", 0);
        $amount = $this->GetGP ("amount", 0);
        $description = $this->enc ($this->GetGP ("description", 0));
        $thisTime = time ();
        $count_db = $this->db->GetOne ("Select COUNT(*) From `members` Where member_id='$member_id'", 0);
        if ($count_db == 1)
        {
            if ($amount > 0 And $description != "" And is_numeric($amount))
            {
                $this->db->ExecuteSql ("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$amount', '0', '0', '$member_id', '$thisTime', '$description', '0')");
                $this->Redirect ($this->pageUrl."?ocd=inout&id=$member_id&res=ok");
            }
            else
            {
                $this->Redirect ($this->pageUrl."?ocd=inout&id=$member_id&res=nod");
            }

        }
        else
        {
            $this->Redirect ($this->pageUrl."?ocd=inout&id=$member_id&res=nom");
        }

    }

    //------------------------------------------------------------------------------
    function ocd_add ()
    {
        $member_id = $this->GetGP ("id", 0);
        $amount = $this->GetGP ("amount", 0);
        $description = $this->enc ($this->GetGP ("description", 0));
        $thisTime = time ();
        $count_db = $this->db->GetOne ("Select COUNT(*) From `members` Where member_id='$member_id'", 0);
        if ($count_db == 1)
        {
            if ($amount > 0 And $description != "" And is_numeric($amount))
            {
                $this->db->ExecuteSql ("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('$amount', '0', '0', '$member_id', '$thisTime', '$description', '0')");
                $this->Redirect ($this->pageUrl."?ocd=inout&id=$member_id&res=ok");
            }
            else
            {
                $this->Redirect ($this->pageUrl."?ocd=inout&id=$member_id&res=nod");
            }

        }
        else
        {
            $this->Redirect ($this->pageUrl."?ocd=cash&id=$member_id&res=nom");
        }

    }

    //------------------------------------------------------------------------------
    function selectSearch ($value = "")
    {
        $toRet = "<select name='s_field' style='width:180px;' maxlength='80'>";
        foreach ($this->searchList as $name => $field)
        $toRet .= ($value == $field) ? "<option value='$field' selected>$name</option>" : "<option value='$field'>$name</option>";
        $toRet .= "</select>";
        return $toRet; 
    }

    //------------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/member_details.tpl";
        $id = $this->GetGP ("id");
        $level_id = $this->db->GetOne ("Select m_level From `members` Where member_id='$id'", 0);

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where member_id=$id", $this->pageUrl);
                $ip_check = ($row["ip_check"] == 1)? "<input type='checkbox' name='ip_check' value='1' checked> Secury mode" : "";
                $username = "<input type='text' name='username' value='".$row["username"]."' maxlength='120' style='width: 300px;'>";
                $first_name = "<input type='text' name='first_name' value='".$row["first_name"]."' maxlength='120' style='width: 300px;'>";
                $last_name = "<input type='text' name='last_name' value='".$row["last_name"]."' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='".$row["email"]."' maxlength='120' style='width: 300px;'>";
                $enroller_id = "<input type='text' name='enroller_id' value='".$row["enroller_id"]."' maxlength='12' style='width: 300px;'>";
//                $m_level = ($level_id == 0)? $row["m_level"] : "<input type='text' name='m_level' value='".$row["m_level"]."' maxlength='12' style='width: 300px;'>";

                $m_level = ($level_id == 0)? $row["m_level"] : $this->SelectLevel ($row["m_level"], $id);

//                $passwd = "<input type='text' name='passwd' value='".$row["passwd"]."' maxlength='12' style='width: 300px;'>";

                $passwd = "Coded";
                $street = "<input type='text' name='street' value='".$row['street']."' style='width:300px;' maxlength='50'>";
                $city = "<input type='text' name='city' value='".$row['city']."' style='width:300px;' maxlength='50'>";
                $state = "<input type='text' name='state' value='".$row['state']."' style='width:300px;' maxlength='50'>";
                $country = "<input type='text' name='country' value='".$row['country']."' style='width:300px;' maxlength='50'>";
                $postal = "<input type='text' name='postal' value='".$row['postal']."' style='width:300px;' maxlength='50'>";
                $phone = "<input type='text' name='phone' value='".$row['phone']."' style='width:300px;' maxlength='50'>";
                $account_id = "<input type='text' name='account_id' value='".$row['account_id']."' style='width:190px;' maxlength='150'>";
                $processor = $row['processor'];

                break;

            case FORM_FROM_GP:

                $ip_check = ($this->GetGP("ip_check", 0) == 1)? "<input type='checkbox' name='ip_check' value='1' checked> Secury mode" : "";
                $username = "<input type='text' name='username' value='".$this->GetGP("username")."' maxlength='120' style='width: 300px;'>";
                $first_name = "<input type='text' name='first_name' value='".$this->GetGP("first_name")."' maxlength='120' style='width: 300px;'>";
                $last_name = "<input type='text' name='last_name' value='".$this->GetGP("last_name")."' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='".$this->GetGP("email")."' maxlength='120' style='width: 300px;'>";
                $enroller_id = "<input type='text' name='enroller_id' value='".$this->GetGP("enroller_id")."' maxlength='12' style='width: 300px;'>";

                $passwd = $this->db->GetOne ("Select passwd From `members` Where member_id='$id'", "");
                $passwd = ($passwd == "")? "<input type='text' name='passwd' value='".$this->GetGP("passwd")."' maxlength='12' style='width: 300px;'>" : "Coded";

//                $m_level = ($level_id == 0)? $row["m_level"] : "<input type='text' name='m_level' value='".$this->GetGP("m_level")."' maxlength='12' style='width: 300px;'>";

                $m_level = ($level_id == 0)? "" : $this->SelectLevel ($this->GetGP("m_level"), $id);

                $street = "<input type='text' name='street' value='".$this->GetGP("street")."' style='width:300px;' maxlength='50'>";
                $city = "<input type='text' name='city' value='".$this->GetGP("city")."' style='width:200px;' maxlength='50'>";
                $state = "<input type='text' name='state' value='".$this->GetGP("state")."' style='width:300px;' maxlength='50'>";
                $country = "<input type='text' name='country' value='".$this->GetGP("country")."' style='width:300px;' maxlength='50'>";
                $postal = "<input type='text' name='postal' value='".$this->GetGP("postal")."' style='width:300px;' maxlength='50'>";
                $phone = "<input type='text' name='phone' value='".$this->GetGP("phone")."' style='width:300px;' maxlength='50'>";
                $account_id = "<input type='text' name='account_id' value='".$this->GetGP("account_id")."' style='width:190px;' maxlength='150'>";
                $processor = $this->GetGP("processor");

                break;

            case FORM_EMPTY:
            default:

                $ip_check = "";
                $username = "<input type='text' name='username' value='' maxlength='120' style='width: 300px;'>";
                $first_name = "<input type='text' name='first_name' value='' maxlength='120' style='width: 300px;'>";
                $last_name = "<input type='text' name='last_name' value='' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='' maxlength='120' style='width: 300px;'>";
                $enroller_id = "<input type='text' name='enroller_id' value='' maxlength='12' style='width: 300px;'>";
                $passwd = "<input type='text' name='passwd' value='' maxlength='12' style='width: 300px;'>";
                $m_level = "";
                $street = "<input type='text' name='street' value='' style='width:300px;' maxlength='50'>";
                $city = "<input type='text' name='city' value='' style='width:200px;' maxlength='50'>";
                $state = "<input type='text' name='state' value='' style='width:200px;' maxlength='50'>";
                $country = "<input type='text' name='country' value='' style='width:300px;' maxlength='50'>";
                $postal = "<input type='text' name='postal' value='' style='width:300px;' maxlength='50'>";
                $phone = "<input type='text' name='phone' value='' style='width:300px;' maxlength='50'>";
                $account_id = "<input type='text' name='account_id' value='' style='width:300px;' maxlength='150'>";
                $processor = 0;

                break;
        }
        
        $reg_date = $this->db->GetOne ("Select reg_date From `members` Where member_id='$id'", 0);
        $last_access = $this->db->GetOne ("Select last_access From `members` Where member_id='$id'", 0);
        $sponsored = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$id'", 0);
        $earned = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='$id'", 0);
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_USERNAME" => $username,
            "MAIN_USERNAME_ERROR" => $this->GetError ("username"),
            "MAIN_FIRST_NAME" => $first_name,
            "MAIN_FIRST_NAME_ERROR" => $this->GetError ("first_name"),
            "MAIN_LAST_NAME" => $last_name,
            "MAIN_LAST_NAME_ERROR" => $this->GetError ("last_name"),
            "MAIN_EMAIL" => $email,
            "MAIN_EMAIL_ERROR" => $this->GetError ("email"),
            "MAIN_SPONSOR" => $enroller_id,
            "MAIN_SPONSOR_ERROR" => $this->GetError ("enroller_id"),
            "MAIN_LEVEL" => $m_level,
            "MAIN_LEVEL_ERROR" => $this->GetError ("m_level"),
            "MAIN_PASSWD" => $passwd,
            "MAIN_PASSWD_ERROR" => $this->GetError ("passwd"),
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
            "MAIN_IP" => $ip_check,
            
            "MAIN_PROCESSOR" => $this->selectProcessor ($processor),
            "MAIN_ACCOUNT_ID" => $account_id,

            "MAIN_ADDRESS" => $street,
            "MAIN_CITY" => $city,
            "MAIN_STATE" => $state,
            "MAIN_COUNTRY" => $country,
            "MAIN_POSTAL" => $postal,
            "MAIN_PHONE" => $phone,

            "MAIN_REG" => ($reg_date > 0)? date ("d-M-Y H:i", $reg_date) : "N/A",
            "MAIN_LAST" => ($last_access > 0)? date ("d-M-Y H:i", $last_access) : "Not logged in yet",
            "MAIN_SPONSORED" => $sponsored,
            "MAIN_EARNED" => $earned,
        );
    }

    //------------------------------------------------------------------------------

    function SelectLevel ($m_level, $id)
    {
        $cycling = $this->db->GetSetting ("cycling", 0);

        if ($cycling == 1)
        {
            $all_levels = $this->db->GetOne ("Select Count(*) From `types`");
            $toRet = "<select name='m_level' style='width:50px;'> \r\n";
            $toRet .= "<option value='$m_level' selected>$m_level</option>";
            for ($i = $m_level + 1; $i <= $all_levels; $i += 1)
            {
                $toRet .= "<option value='$i'>$i</option>";
            }
            return $toRet."</select>\r\n";
        }
        else
        {
            $all_levels = $this->db->GetOne ("Select Count(*) From `types`");

            $toRet = "<select name='m_level' style='width:50px;'> \r\n";

            for ($i = 1; $i <= $all_levels; $i += 1)
            {
                $selected = ($i == $m_level)? "selected" : "";
                $toRet .= "<option value='$i' $selected>$i</option>";
            }
            return $toRet."</select>\r\n";    
        }
    }

    //------------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);

        $this->db->ExecuteSql ("Update `{$this->object}` Set is_dead=(1-is_dead) Where member_id='$id'");
        $is_dead = $this->db->GetOne ("Select is_dead From `{$this->object}` Where member_id='$id'", 0);
        if ($is_dead == 1) $this->db->ExecuteSql ("Update `{$this->object}` Set enroller_id=1 Where enroller_id='$id'");

        $this->Redirect ($this->pageUrl);
    }
    
    //------------------------------------------------------------------------------
    function ocd_delforever ()
    {
        $id = $this->GetGP ("id", 0);
        $enr_id = $this->db->GetOne ("Select `enroller_id` From `members` Where `member_id`='$id'");

		$cycling = $this->db->GetSetting ("cycling", 0);
		if ($cycling == 0)
		{
        		$this->db->ExecuteSql ("Delete From `payins` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `tickets` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `cash` Where to_id='$id'");
        		$this->db->ExecuteSql ("Delete From `cash_out` Where user_id='$id'");
        		$this->db->ExecuteSql ("Delete From `selected` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `ptools` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `replicas` Where member_id='$id'");
        		
        		out_matrix ($id, $enr_id);
        		
        		$this->db->ExecuteSql ("Delete From `members` Where member_id='$id'");
		}
		else
		{
				
				$this->db->ExecuteSql ("Delete From `payins` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `tickets` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `cash` Where to_id='$id'");
        		$this->db->ExecuteSql ("Delete From `cash_out` Where user_id='$id'");
        		$this->db->ExecuteSql ("Delete From `selected` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `ptools` Where member_id='$id'");
        		$this->db->ExecuteSql ("Delete From `replicas` Where member_id='$id'");
        		
        		$this->db->ExecuteSql ("Delete From `members` Where member_id='$id'");
        		
//				$this->db->ExecuteSql ("Update `{$this->object}` Set is_dead=1 Where member_id='$id'");		
		}

        $physical_path = $this->db->GetSetting ("PathSite");
        $file = $physical_path."data/avatar/avatar_$id.jpg";
        @unlink($file);

        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where member_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Add New Member";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Members List</a> / Add New Member";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //------------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "Add New Member";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Members List</a> / Add a new member";
        $username = $this->enc ($this->GetValidGP ("username", "Username", VALIDATE_USERNAME));
        $first_name = $this->enc ($this->GetValidGP ("first_name", "First name", VALIDATE_NOT_EMPTY));
        $last_name = $this->enc ($this->GetValidGP ("last_name", "Last name", VALIDATE_NOT_EMPTY));
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));
        $enroller_id = $this->enc ($this->GetValidGP ("enroller_id", "Sponsor's ID", VALIDATE_INT_POSITIVE));
        $passwd = $this->GetValidGP ("passwd", "Password", VALIDATE_PASSWORD);
        $password_code = md5 ($passwd);
        $m_level = 0;

        $street = $this->enc ($this->GetGP ("street", ""));
        $city = $this->enc ($this->GetGP ("city", ""));
        $state = $this->enc ($this->GetGP ("state", ""));
        $country = $this->enc ($this->GetGP ("country", ""));
        $postal = $this->enc ($this->GetGP ("postal", ""));
        $phone = $this->enc ($this->GetGP ("phone", ""));

        $processor = $this->GetGP ("processor", 0);
        $account_id = $this->GetGP ("account_id", "");

        $count_username = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where username='$username'", 0);
        if ($count_username > 0)
        {
            $this->SetError ("username", "The member with this Username is already registered. Please choose another.");
        }
        $count_email = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where email='$email'", 0);
        $count_enroller = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$enroller_id'", 0);
        $count_all = $this->db->GetOne ("Select Count(*) From `{$this->object}`", 0);

        if ($count_enroller == 0 And $count_all != 0)
        {
            $this->SetError ("enroller_id", "No member in Matrix with this ID.");
        }
        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $cycling = $this->db->GetSetting ("cycling", 0);
            $is_active = ($cycling == 1)? 1 : 0;
            $this->db->ExecuteSql ("Insert into `{$this->object}` (username, email, enroller_id, m_level, first_name, last_name, passwd, reg_date, is_active, street, city, state, country, postal, phone, processor, account_id) values ('$username', '$email', '$enroller_id', '$m_level', '$first_name', '$last_name', '$password_code', '".time()."', '$is_active', '$street', '$city', '$state', '$country', '$postal', '$phone', '$processor', '$account_id')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //------------------------------------------------------------------------------
    function ocd_inmatrix ()
    {
        $member_id = $this->GetGP ("id", 0);
        $enroller_id = $this->db->GetOne ("Select enroller_id From `{$this->object}` Where member_id='$member_id'", 0);
        $level_id = $this->db->GetOne ("Select m_level From `{$this->object}` Where member_id='$member_id'", 1);
        $this->db->ExecuteSql ("Update `{$this->object}` Set is_active=1 Where member_id='$member_id'", 0);
        pre_in_matrix ($member_id, $enroller_id, $level_id);
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_out_matrix ()
    {
        $member_id = $this->GetGP ("id", 0);
        $enroller_id = $this->db->GetOne ("Select enroller_id From `{$this->object}` Where member_id='$member_id'", 0);
        out_matrix ($member_id, $enroller_id);
        $this->db->ExecuteSql ("Update `{$this->object}` Set is_active=0, is_dead=1 Where member_id='$member_id'", 0);
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_pay ()
    {
        $id = $this->GetGP ("id");
        $level_id = $this->db->GetOne ("Select m_level From `members` Where member_id='$id'");
        $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$id'");

        $cycling = $this->db->GetSetting ("cycling", 0);
        if ($cycling == 1)
        {
            $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
            if ($enr_level == 0)
            {
                $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$id'");
            }
            $thisTime = time ();
            payUpline ($id, $thisTime, $level_id, '-1');
        }
        else
        {
            $this->db->ExecuteSql ("Update `members` Set m_level='1', is_active=1 Where member_id='$id'");
            in_forced_matrix ($id, $enroller_id);
        }
        $this->Redirect ($this->pageUrl."?m=yes");
    }

    //------------------------------------------------------------------------------
    function ocd_pay_all ()
    {
        $cycling = $this->db->GetSetting ("cycling", 0);
        if ($cycling == 1)
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_dead=0 And is_active=1 And m_level=0 Order By member_id Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['member_id'];
                $level_id = $row['m_level'];
                $enroller_id = $row['enroller_id'];
                $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                if ($enr_level == 0)
                {
                    $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And is_dead=0 And m_level>0 Order By RAND() Limit 1", 1);
                    $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$id'");
                }
                $thisTime = time ();
                payUpline ($id, $thisTime, $level_id, '-1');
            }
            $this->db->FreeSqlResult ($result);
            $this->Redirect ($this->pageUrl."?m=yes");
        }
        else
        {
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where is_dead=0 And is_active=0 And m_level=0 Order By member_id Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['member_id'];
                $level_id = $row['m_level'];
                $enroller_id = $row['enroller_id'];
                $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");
                if ($enr_level == 0)
                {
                    $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And is_dead=0 And m_level>0 Order By RAND() Limit 1", 1);
                    $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$id'");
                }
                $this->db->ExecuteSql ("Update `members` Set m_level='1', is_active=1 Where member_id='$id'");
                in_forced_matrix ($id, $enroller_id);
             }
            $this->db->FreeSqlResult ($result);
            $this->Redirect ($this->pageUrl."?m=yes");       
        }
    }

    //------------------------------------------------------------------------------
    function ocd_edit ()
    {   
        $this->pageTitle = "Edit";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Members List</a> / Edit member";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //------------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Members</a> / Edit member";
        $id = $this->GetGP ("id");
        $ip_check = $this->GetGP ("ip_check", 0);
        $username = $this->enc ($this->GetValidGP ("username", "Username name", VALIDATE_USERNAME));
        $first_name = $this->enc ($this->GetValidGP ("first_name", "First name", VALIDATE_NOT_EMPTY));
        $last_name = $this->enc ($this->GetValidGP ("last_name", "Last name", VALIDATE_NOT_EMPTY));
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));
        $enroller_id = $this->enc ($this->GetValidGP ("enroller_id", "Sponsor's ID", VALIDATE_INT_POSITIVE));
        $m_level = $this->GetGP ("m_level");
        
        $street = $this->enc ($this->GetGP ("street", ""));
        $city = $this->enc ($this->GetGP ("city", ""));
        $state = $this->enc ($this->GetGP ("state", ""));
        $country = $this->enc ($this->GetGP ("country", ""));
        $postal = $this->enc ($this->GetGP ("postal", ""));
        $phone = $this->enc ($this->GetGP ("phone", ""));

        $processor = $this->GetGP ("processor", 0);
        $account_id = $this->GetGP ("account_id", "");

        $card_number = $this->GetGP ("card_number", "");
        $card_shipped = $this->GetGP ("card_shipped", "");
        $card_login = $this->GetGP ("card_login", "");

        $username_db = $this->db->GetOne ("Select username From `{$this->object}` Where member_id='$id'", 0);
        if ($username_db != $username)
        {
            $count_username = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where username='$username'", 0);
            if ($count_username > 0)
            {
                $this->SetError ("username", "The member with this Username is already registered. Please choose another.");
            }
        }

        $email_db = $this->db->GetOne ("Select email From `{$this->object}` Where member_id='$id'", 0);
        if ($email_db != $email)
        {
            $count_email = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where email='$email'", 0);
            if ($count_email > 0)
            {
                $this->SetError ("email", "The member with this Email is already registered. Please choose another.");
            }
        }

        $enroller_db = $this->db->GetOne ("Select enroller_id From `{$this->object}` Where member_id='$id'", 0);
        if ($enroller_db != $enroller_id)
        {
            $count_enroller = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$enroller_id'", 0);
            if ($count_enroller == 0)
            {
                $this->SetError ("enroller_id", "No member in matrix with this ID.");
            }
            if ($enroller_id == $id)
            {
                $this->SetError ("enroller_id", "One cannot be enroller to himself.");
            }
        }

        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update `{$this->object}` Set username='$username', first_name='$first_name', enroller_id='$enroller_id', m_level='$m_level', last_name='$last_name', email='$email', street='$street', city='$city', state='$state', country='$country', postal='$postal', phone='$phone', processor='$processor', account_id='$account_id' Where member_id='$id'");

            $cycling = $this->db->GetSetting ("cycling", 0);
            if ($cycling == 1)
            {
                $level_past = $this->db->GetOne ("Select m_level From `members` Where member_id='$id'", 0);
                $this->db->ExecuteSql ("Insert Into `matrix` (host_id, m_level, host_matrix) Values ('$id', '$m_level', '1')");
                if ($level_past < $m_level) pre_in_matrix ($id, $enroller_id, $m_level);
                $this->Redirect ($this->pageUrl);
            }
            else
            {
                $this->db->ExecuteSql ("Update `matrix` Set m_level='$m_level' Where member_id='$id'");
                $this->Redirect ($this->pageUrl);
            }
        }
    }

    //--------------------------------------------------------------------------
    function selectProcessor ($value = 0)
    {
        $toRet = "<select name='processor' style='width:200px;'> \r\n";
        $result = $this->db->ExecuteSql ("Select processor_id, name From `processors` Where is_active=1 Order By name");

        $selected = ($value == 0) ? "selected" : "";
        $toRet .= "<option value='0' $selected>Select processor</option>";
        while ($row = $this->db->FetchInArray ($result))
        {
            $selected = ($row['processor_id'] == $value) ? "selected" : "";
            $toRet .= "<option value='".$row['processor_id']."' $selected>".$row['name']."</option>";
        }
        return $toRet."</select>\r\n";    
    }

    //------------------------------------------------------------------------------
    function ocd_assign ()
    {

        $total = $this->db->GetOne ("Select Count(*) From `members` Where is_dead=0 And member_id>1", 0);
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select member_id, enroller_id From `members` Where member_id>1 And is_dead=0 Order By member_id ASC", 0);
            while ($row = $this->db->FetchInArray ($result))
            {
                $member_id = $row ["member_id"];
                $enroller_id = $row ["enroller_id"];
                $count = $this->db->GetOne ("select Count(*) From `matrix` Where member_id='$member_id'", 0);

                $count_m = $this->db->GetOne ("select Count(*) From `members` Where member_id='$enroller_id'", 0);
                if ($count == 0 And $count_m == 1)
                {
                    $this->db->ExecuteSql ("Update `{$this->object}` Set is_active=1 Where member_id='$member_id'", 0);
                    pre_in_matrix ($member_id, $enroller_id, 1);
                }
            }
            $this->db->FreeSqlResult ($result);
        }
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_reset_all ()
    {

        $cycling = $this->db->GetSetting ("cycling", 0);

        $this->db->ExecuteSql ("TRUNCATE TABLE `matrix`");
        $this->db->ExecuteSql ("TRUNCATE TABLE `payins`");
        $this->db->ExecuteSql ("TRUNCATE TABLE `cash_out`");
        $this->db->ExecuteSql ("TRUNCATE TABLE `cash`");
        $this->db->ExecuteSql ("TRUNCATE TABLE `sponsor_bonus`");
        
        $this->db->ExecuteSql ("TRUNCATE TABLE `matrices_completed`");
        $this->db->ExecuteSql ("TRUNCATE TABLE `places`");

        if ($cycling == 1)
        {
            $this->db->ExecuteSql ("Update `members` Set m_level=0");
            $this->db->ExecuteSql ("Update `members` Set m_level=1, is_active=1 Where member_id=1");
            
            $this->db->ExecuteSql ("Insert Into `places` (member_id, referrer_place_id, m_level, z_date, reentry) Values (1, 0, 1, UNIX_TIMESTAMP(), 1)");

//            $amount = $this->db->GetOne ("Select entrance_fee From `matrixes` Where matrix_id='2'", 0);
//            $this->db->ExecuteSql ("Insert Into `payins` Values (1, 1, UNIX_TIMESTAMP(), '$amount', -1, 'Member Payment', UNIX_TIMESTAMP())");
        }
        else
        {
            $this->db->ExecuteSql ("Update `members` Set m_level=0, is_active=0");
            $this->db->ExecuteSql ("Update `members` Set m_level=1, is_active=1 Where member_id=1");
            $this->db->ExecuteSql ("Insert Into `matrix` Values (1, 1, 0, 0, 0, 0, 1, 0, UNIX_TIMESTAMP(), 0)");
        }
        $this->Redirect ($this->pageUrl);
    }
    
    //------------------------------------------------------------------------------
    function ocd_tomem ()
    {
        $id = $this->GetGP ("id", 0);
        $_SESSION['MemberID'] = $id;
        
        $this->Redirect ("../member/myaccount.php");
        
    }

    //------------------------------------------------------------------------------
    function ocd_pass ()
    {
        $id = $this->GetGP ("id", 0);
        $password = getUnID (8);

        $password_code = md5 ($password);
        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $this->db->ExecuteSql ("Update `members` Set passwd='$password_code' Where member_id='$id'");

        $email = $this->db->GetOne ("Select email From `members` Where member_id='$id'", "");
        $username = $this->db->GetOne ("Select username From `members` Where member_id='$id'", "");
        $fname = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$id'", "");

        $subject = "New access details From $siteTitle";

        $message = "Dear $fname,\r\n\r\n";
        $message .= "Your access details to your account on $siteTitle have been changed:\r\n";
        $message .= "Username : $username\r\n";
        $message .= "Password : $password\r\n\r\n";
        $message .= "Sorry for inconvenience,\r\n";
        $message .= $siteTitle;

        sendMail ($email, $subject, $message, $this->emailHeader);

        $adminEmail = $this->db->GetSetting ("ContactEmail");

        $subject = "Member #$id access settings";

        $message = "Admin,\r\n\r\n";
        $message .= "Access details of member $id have been changed:\r\n";
        $message .= "Username : $username\r\n";
        $message .= "Password : $password\r\n\r\n";
        $message .= $siteTitle;

        sendMail ($adminEmail, $subject, $message, $this->emailHeader);
        $this->Redirect ($this->pageUrl);
    }
    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("members");

$zPage->Render ();

?>
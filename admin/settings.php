<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_admin.php");
require_once("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage($object)
    {
        $this->orderDefault = "Title";
        XPage::XPage($object);
        $this->mainTemplate = "./templates/settings.tpl";
        $this->pageTitle = "Site Settings";
        $this->pageHeader = "Site Settings";
    }

    //--------------------------------------------------------------------------
    function ocd_list()
    {
        $message = "";
        if ($this->GetGP("ec") == "done") $message = "Changes were successfully saved";

        $siteTitle = $this->db->GetSetting("SiteTitle");
        $siteTitle = "<input type='text' name='SiteTitle' value='$siteTitle' style='width:300px;'>";
        $siteUrl = $this->db->GetSetting("SiteUrl");
        $siteUrl = "<input type='text' name='SiteUrl' value='$siteUrl' style='width:300px;'>";
        $pathSite = $this->db->GetSetting("PathSite");
        $pathSite = "<input type='text' name='PathSite' value='$pathSite' style='width:300px;'>";

        $currency = $this->db->GetSetting("currency");
        $currency_rate = $this->db->GetSetting("currency_rate");
        //$currency = "<input type='text' name='currency' value='$currency' style='width:30px;'>";

        $result = $this->db->ExecuteSql("Select * From `currency` Where active=1", "");
        $currency = " <select name='currency'>";
        while ($row = $this->db->FetchInArray($result)) {
            $currency .= "<option value='" . $row['id'] . "' " . ($this->db->GetSetting("currency") == $row['id'] ? 'selected' : '') . " >" . $row['name'] . "</option>";
        }
        $currency .= "</select>";

        $currency = "
        <tr>
            <td width='400'><span class='signs_b'>Currency:</span></td>
            <td width='20'>
                <span title=\"Choose the symbol of currency you will use for payments\" class=\"vtip\"><img src='./images/question.png'></span>
            </td>
            <td> $currency</td>
        </tr>
        ";
        if ($this->LicenseAccess['currency'][$this->lic_key] == 0) $currency = '';

        $REFERRER_URL = "<select name='REFERRER_URL'>";
        $REFERRER_URL .= "<option value='member_id' " . ($this->db->GetSetting("ReferrerUrl") == 'member_id' ? 'selected' : '') . " >Id</option>";
        $REFERRER_URL .= "<option value='username' " . ($this->db->GetSetting("ReferrerUrl") == 'username' ? 'selected' : '') . " >Username</option>";
        $REFERRER_URL .= "</select>";


        $COMMISSIONS_VALUE = "<select name='COMMISSIONS_VALUE'>";
        $COMMISSIONS_VALUE .= "<option value='1' " . ($this->db->GetSetting("Commissions_Value") == '1' ? 'selected' : '') . " >$</option>";
        $COMMISSIONS_VALUE .= "<option value='2' " . ($this->db->GetSetting("Commissions_Value") == '2' ? 'selected' : '') . " >%</option>";
        $COMMISSIONS_VALUE .= "</select>";

        $SPONSOR_VALUE = "<select name='SPONSOR_VALUE'>";
        $SPONSOR_VALUE .= "<option value='1' " . ($this->db->GetSetting("SPONSOR_VALUE") == '1' ? 'selected' : '') . " >$</option>";
        $SPONSOR_VALUE .= "<option value='2' " . ($this->db->GetSetting("SPONSOR_VALUE") == '2' ? 'selected' : '') . " >%</option>";
        $SPONSOR_VALUE .= "</select>";

        $WITHDRAWAL_VALUE = "<select name='WITHDRAWAL_VALUE'>";
        $WITHDRAWAL_VALUE .= "<option value='1' " . ($this->db->GetSetting("WITHDRAWAL_VALUE") == '1' ? 'selected' : '') . " >$</option>";
        $WITHDRAWAL_VALUE .= "<option value='2' " . ($this->db->GetSetting("WITHDRAWAL_VALUE") == '2' ? 'selected' : '') . " >%</option>";
        $WITHDRAWAL_VALUE .= "</select>";

        $product = $this->db->GetSetting("product");
        $product = "<input type='text' name='product' value='$product' style='width:300px;'>";

        $turn_date = $this->db->GetSetting("PaymentModeDate");
        $mincashout = sprintf("%01.2f", $this->db->GetSetting("MinCashOut"));
        $mincashout = "<input type='text' name='Mincashout' value='$mincashout' maxlength='6' style='width:50px;'>";

        $sponsor_amount = sprintf("%01.2f", $this->db->GetSetting("sponsor_amount"));
        $sponsor_amount = "<input type='text' name='sponsor_amount' value='$sponsor_amount' maxlength='6' style='width:50px;'>";

        $sponsor_quant = $this->db->GetSetting("sponsor_quant");
        $sponsor_quant = "<input type='text' name='sponsor_quant' value='$sponsor_quant' maxlength='6' style='width:50px;'>";

        $fee = sprintf("%01.2f", $this->db->GetSetting("fee"));

        if ($this->lic_key == 'FREE') $fee = $fee;
        else $fee = "<input type='text' name='fee' value='$fee' maxlength='6' style='width:50px;'>";

        $mode = $this->db->GetSetting("cycling");
        $ch2 = ($mode == 1) ? "checked" : "";
        $ch1 = ($mode == 0) ? "checked" : "";

        $matrix_title = ($mode == 1) ? "Cycling Matrix" : "Forced Matrix";

        $type = "<input type='radio' name='type' value=0 $ch1> Forced matrix <br /><input type='radio' name='type' value=1 $ch2> Cycling matrix";

        $replica = ($this->db->GetSetting("is_replica", 0) == 1) ? "<input type='checkbox' name='is_replica' value='1' checked>" : "<input type='checkbox' name='is_replica' value='1'>";
        $replica = "
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Replicated Sites Accessibility:</span></td>
        <td width='20' valign='top'>
            <span title='Replicated Sites Accessibility (powerfull promo tool for members)' class='vtip'><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            $replica
        </td>
    </tr>
        ";
        if ($this->LicenseAccess['is_replica'][$this->lic_key] == 0) $replica = '';

        $ptools = ($this->db->GetSetting("useBanners", 0) == 1) ? "<input type='checkbox' name='ptools' value='1' checked>" : "<input type='checkbox' name='ptools' value='1'>";
        $ptools = "
            <tr>
                <td width='400' valign='top'><span class='signs_b'>Allow banners for members:</span></td>
                <td width='20' valign='top'>
                    <span title='This option lets users create banners to promote their accounts.' class='vtip'><img src='./images/question.png'></span>
                </td>
                <td valign='top'>
                    $ptools
                </td>
            </tr>
        ";
        if ($this->LicenseAccess['ptools'][$this->lic_key] == 0) $ptools = '';

        $quant_replica = $this->db->GetSetting("quant_replica");
        $quant_replica = "
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Max number of the members pages:</span></td>
        <td width='20' valign='top'>
            <span title='Each member can get this max number of pages in Replica site section.' class='vtip'><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            <input type='text' name='quant_replica' value='$quant_replica' maxlength='2' style='width:50px;'>
        </td>
    </tr>
            ";
        if ($this->LicenseAccess['quant_replica'][$this->lic_key] == 0) $quant_replica = '';

        $useSecureMembers = ($this->db->GetSetting("useSecureMembers", 0) == 1) ? "<input type='checkbox' name='useSecureMembers' value='1' checked />" : "<input type='checkbox' name='useSecureMembers' value='1' />";
        $useAutoresponder = ($this->db->GetSetting("useAutoresponder", 0) == 1) ? "<input type='checkbox' name='useAutoresponder' value='1' checked />" : "<input type='checkbox' name='useAutoresponder' value='1' />";

        $useEshop = ($this->db->GetSetting("useEshop", 0) == 1) ? "<input type='checkbox' name='useEshop' value='1' checked />" : "<input type='checkbox' name='useEshop' value='1' />";
        $useEshop = "
        <tr>
            <td width='400'><span class='signs_b'>Use E-Shop Function:</span></td>
            <td width='20'>
                <span title='Members will be able to buy/download downloadable products accordingly to their level.' class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> $useEshop</td>
        </tr>
        ";
        if ($this->LicenseAccess['categories'][$this->lic_key] == 0) $useEshop = '';

        $useValidation = ($this->db->GetSetting("useValidation", 0) == 1) ? "<input type='checkbox' name='useValidation' value='1' checked />" : "<input type='checkbox' name='useValidation' value='1' />";
        $useValidation = "<tr>
            <td width='400'><span class='signs_b'>Use Email Validation after sign up:</span></td>
            <td width='20'>
                <span title='Use Email Validation After Sign Up.' class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> $useValidation</td>
        </tr>
        ";
        if ($this->LicenseAccess['useValidation'][$this->lic_key] == 0) $useValidation = '';

        $quant_textadds = $this->db->GetSetting("quant_textadds");
        $quant_textadds = "<input type='text' name='quant_textadds' value='$quant_textadds' maxlength='2' style='width:50px;'>";

        $quant_textadds_show = $this->db->GetSetting("quant_textadds_show");
        $quant_textadds_show = "<input type='text' name='quant_textadds_show' value='$quant_textadds_show' maxlength='2' style='width:50px;'>";

        $quant_textadds_show_m = $this->db->GetSetting("quant_textadds_show_m");
        $quant_textadds_show_m = "<input type='text' name='quant_textadds_show_m' value='$quant_textadds_show_m' maxlength='2' style='width:50px;'>";

        $number_turing = $this->db->GetSetting("number_turing", "0");
        $number_turing = "<input type='text' name='number_turing' value='$number_turing' maxlength='1' style='width:20px;'>";
        $number_turing = "
    <tr>
        <td width='400'><span class='signs_b'>Number of Symbols in \"Turing Number\":</span></td>
        <td width='20'>
            <span title=\"Number of Symbols in 'Turing Number'. Enter '0' and save changes to disable this feature.\" class='vtip'><img src='./images/question.png'></span>
        </td>
        <td> $number_turing&nbsp; <span class='error'></span></td>
    </tr>
        ";
        if ($this->LicenseAccess['turing_number'][$this->lic_key] == 0) $number_turing = '';

        $payp_fromcash = ($this->db->GetSetting("payp_fromcash", 0) == 1) ? "<input type='checkbox' name='payp_fromcash' value='1' checked>" : "<input type='checkbox' name='payp_fromcash' value='1'>";
        $payp_fromcash = "
    <tr>
        <td width='400'><span class='signs_b'>Allow members to pay for E-Shop products using \"Account Cash Balance\":</span></td>
        <td width='20'>
            <span title=\"Allow members to pay for E-Shop products using 'Account Cash Balance'.\" class=\"vtip\"><img src='./images/question.png'></span>
        </td>
        <td> $payp_fromcash</td>
    </tr>
        ";
        if ($this->LicenseAccess['payp_fromcash'][$this->lic_key] == 0) $payp_fromcash = '';

        $is_pif = ($this->db->GetSetting("is_pif", 0) == 1) ? "<input type='checkbox' name='is_pif' value='1' checked />" : "<input type='checkbox' name='is_pif' value='1' />";
        $is_pif = "
    <tr>
        <td width='400'><span class='signs_b'>Allow members to pay for other members (PIF feature)</span></td>
        <td width='20'>
            <span title='Allow members to pay for other members (Pay It Forward feature).' class='vtip'><img src='./images/question.png'></span>
        </td>
        <td> $is_pif</td>
    </tr>
        ";
        if ($this->LicenseAccess['is_pif'][$this->lic_key] == 0) $is_pif = '';

        $is_pif_cash = ($this->db->GetSetting("is_pif_cash", 0) == 1) ? "<input type='checkbox' name='is_pif_cash' value='1' checked />" : "<input type='checkbox' name='is_pif_cash' value='1' />";
        $is_pif_cash = "
    <tr>
        <td width='400'><span class='signs_b'>Allow members to pay for other members using account cash balance (if PIF is activated)</span></td>
        <td width='20'>
            <span title='Allow members to pay for other members  (Pay It Forward feature) using account cash balance.' class='vtip'><img src='./images/question.png'></span>
        </td>
        <td> $is_pif_cash</td>
    </tr>
        ";
        if ($this->LicenseAccess['is_pif_cash'][$this->lic_key] == 0) $is_pif_cash = '';


        $is_random = $this->db->GetSetting("is_random", 0);
        if ($is_random == 1) {
            $main_random = "<input type='radio' name='is_random' value='1' checked='checked' /> assign a random sponsor from active members; <br /><input type='radio' name='is_random' value=0>  assign Admin as a sponsor;";
        } else {
            $main_random = "<input type='radio' name='is_random' value='1' /> assign a random sponsor from active members; <br /><input type='radio' name='is_random' value='0' checked='checked' />  assign Admin as a sponsor;";
        }


        $MATCHING_BONUS = ($this->db->GetSetting("matching_bonus") == 1 ? ' checked' : '');
        $MATCHING_BONUS_VALUE = $this->db->GetSetting("matching_bonus_value");

        $this->data = array(
            "ACTION_SCRIPT" => $this->pageUrl,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONFIRM" => $message,
            "MAIN_SITE_TITLE" => $siteTitle,
            "MAIN_SITE_URL" => $siteUrl,
            "MAIN_PATH_SITE" => $pathSite,
            "MAIN_CURRENCY" => $currency,
            "MAIN_CURRENCY_RATE" => $currency_rate,
            "MAIN_PRODUCT" => $product,
            "MAIN_FEE" => $fee,
            "MAIN_CASH_OUT" => $mincashout,
            "MAIN_TYPE" => $type,
            "SPONSOR_AMOUNT" => $sponsor_amount,
            "SPONSOR_QUANT" => $sponsor_quant,
            "KIND_OF_MATRIX" => $matrix_title,
            "REPLICA" => $replica,
            "QUANT_REPLICA" => $quant_replica,
            "PTOOLS" => $ptools,
            "MAIN_SECURE" => $useSecureMembers,
            "MAIN_AUTO" => $useAutoresponder,
            "MAIN_ESHOP" => $useEshop,
            "MAIN_VALIDATION" => $useValidation,
            "QUANT_TEXTADDS" => $quant_textadds,
            "QUANT_TEXTADDS_SHOW" => $quant_textadds_show,
            "QUANT_TEXTADDS_SHOW_M" => $quant_textadds_show_m,
            "NUMBER_TURING" => $number_turing,
            "MAIN_PAYPFROMCASH" => $payp_fromcash,
            "IS_PIF" => $is_pif,
            "IS_PIF_CASH" => $is_pif_cash,
            "MAIN_RANDOM" => $main_random,
            "REFERRER_URL" => $REFERRER_URL,
            'COMMISSIONS_VALUE' => $COMMISSIONS_VALUE,
            'MATCHING_BONUS' => $MATCHING_BONUS,
            'MATCHING_BONUS_VALUE' => $MATCHING_BONUS_VALUE,
            "SPONSOR_VALUE" => $SPONSOR_VALUE,
            'WITHDRAWAL_VALUE' => $WITHDRAWAL_VALUE

        );
    }

    //--------------------------------------------------------------------------
    function ocd_update()
    {
        $siteTitle = $this->enc($this->GetValidGP("SiteTitle", "Title", VALIDATE_NOT_EMPTY));
        $siteUrl = $this->GetValidGP("SiteUrl", "Link (URL)", VALIDATE_NOT_EMPTY);
        $pathSite = $this->GetValidGP("PathSite", "Path", VALIDATE_NOT_EMPTY);
        $product = $this->GetValidGP("product", "Product", VALIDATE_NOT_EMPTY);
        $currency = $this->GetValidGP("currency", "currency", VALIDATE_NOT_EMPTY);
        $currency_rate = $this->GetValidGP("currency_rate", "currency_rate", VALIDATE_NOT_EMPTY);
        if ($this->lic_key != 'FREE') $fee = $this->GetValidGP("fee", "Fee", VALIDATE_NOT_EMPTY);
        else $fee = sprintf("%01.2f", $this->db->GetSetting("fee"));
        $mode = $this->GetGP("type", 0);
        $mincashout = $this->GetValidGP("Mincashout", "Min sum of cashout", VALIDATE_NUMERIC_POSITIVE);
        $number_turing = $this->GetValidGP("number_turing", "Number of Symbols in 'Turing Number'", VALIDATE_NUMERIC_POSITIVE);
        if ($number_turing > 4) $this->SetError("number_turing", "Turing number must contain not more than 4 characters");

        $sponsor_amount = $this->GetGP("sponsor_amount", 0);
        $sponsor_quant = $this->GetGP("sponsor_quant", 0);

        $is_replica = $this->GetGP("is_replica", 0);
        $quant_replica = $this->GetGP("quant_replica", 0);

        $quant_textadds = $this->GetGP("quant_textadds", 0);
        if (!is_numeric($quant_textadds)) $quant_textadds = 0;

        $quant_textadds_show = $this->GetGP("quant_textadds_show", 0);
        if (!is_numeric($quant_textadds_show)) $quant_textadds_show = 0;

        $quant_textadds_show_m = $this->GetGP("quant_textadds_show_m", 0);
        if (!is_numeric($quant_textadds_show_m)) $quant_textadds_show_m = 0;

        $REFERRER_URL = $this->GetGP("REFERRER_URL", 'member_id');

        $COMMISSIONS_VALUE = $this->GetGP("COMMISSIONS_VALUE", '1');
        $SPONSOR_VALUE = $this->GetGP("SPONSOR_VALUE", '1');
        $WITHDRAWAL_VALUE = $this->GetGP("WITHDRAWAL_VALUE", '1');

        $matching_bonus = $this->GetGP("matching_bonus", '');
        $matching_bonus_value = (int)$this->GetGP("matching_bonus_value", '100');

        $ptools = $this->GetGP("ptools", 0);

        if (!is_numeric($quant_replica)) $quant_replica = "";

        $useSecureMembers = $this->GetGP("useSecureMembers", 0);
        $useAutoresponder = $this->GetGP("useAutoresponder", 0);
        $useEshop = $this->GetGP("useEshop", 0);
        $useValidation = $this->GetGP("useValidation", 0);
        $payp_fromcash = $this->GetGP("payp_fromcash", 0);
        $is_pif = $this->GetGP("is_pif", 0);
        $is_pif_cash = $this->GetGP("is_pif_cash", 0);
        $is_random = $this->GetGP("is_random", 0);


        if ($this->errors['err_count'] > 0) {
            $ptools = ($ptools == 1) ? "<input type='checkbox' name='ptools' value='1' checked>" : "<input type='checkbox' name='ptools' value='1'>";
            $ptools = "
                <tr>
                    <td width='400' valign='top'><span class='signs_b'>Allow banners for members:</span></td>
                    <td width='20' valign='top'>
                        <span title='This option lets users create banners to promote their accounts.' class='vtip'><img src='./images/question.png'></span>
                    </td>
                    <td valign='top'>
                        $ptools
                    </td>
                </tr>
            ";
            if ($this->LicenseAccess['ptools'][$this->lic_key] == 0) $ptools = '';


            $replica = ($is_replica == 1) ? "<input type='checkbox' name='is_replica' value='1' checked>" : "<input type='checkbox' name='is_replica' value='0'>";
            $replica = "
            <tr>
                <td width='400' valign='top'><span class='signs_b'>Replicated Sites Accessibility:</span></td>
                <td width='20' valign='top'>
                    <span title='Replicated Sites Accessibility (powerfull promo tool for members)' class='vtip'><img src='./images/question.png'></span>
                </td>
                <td valign='top'>
                    $replica
                </td>
            </tr>
            ";
            if ($this->LicenseAccess['is_replica'][$this->lic_key] == 0) $replica = '';

            $QUANT_REPLICA = "
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Max number of the members pages:</span></td>
        <td width='20' valign='top'>
            <span title='Each member can get this max number of pages in Replica site section.' class='vtip'><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            <input type='text' name='quant_replica' value='$quant_replica' maxlength='2' style='width:50px;'>
        </td>
    </tr>
            ";
            if ($this->LicenseAccess['quant_replica'][$this->lic_key] == 0) $QUANT_REPLICA = '';

            $ch2 = ($mode == 1) ? "checked" : "";
            $ch1 = ($mode == 0) ? "checked" : "";
            $type = "<input type='radio' name='type' value=0 $ch1> Forced matrix <br /><input type='radio' name='type' value=1 $ch2> Cycling matrix";

            $useSecureMembers = ($useSecureMembers == 1) ? "<input type='checkbox' name='useSecureMembers' value='1' checked>" : "<input type='checkbox' name='useSecureMembers' value='1'>";

            $useAutoresponder = ($useAutoresponder == 1) ? "<input type='checkbox' name='useAutoresponder' value='1' checked />" : "<input type='checkbox' name='useAutoresponder' value='1' />";
            $useEshop = ($useEshop == 1) ? "<input type='checkbox' name='useEshop' value='1' checked />" : "<input type='checkbox' name='useEshop' value='1' />";
            $useEshop = "
        <tr>
            <td width='400'><span class='signs_b'>Use E-Shop Function:</span></td>
            <td width='20'>
                <span title='Members will be able to buy/download downloadable products accordingly to their level.' class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> $useEshop</td>
        </tr>
        ";
            if ($this->LicenseAccess['categories'][$this->lic_key] == 0) $useEshop = '';

            $useValidation = ($useValidation == 1) ? "<input type='checkbox' name='useValidation' value='1' checked />" : "<input type='checkbox' name='useValidation' value='1' />";
            $useValidation = "<tr>
                <td width='400'><span class='signs_b'>Use Email Validation after sign up:</span></td>
                <td width='20'>
                    <span title='Use Email Validation After Sign Up.' class='vtip'><img src='./images/question.png'></span>
                </td>
                <td> $useValidation</td>
            </tr>
            ";
            if ($this->LicenseAccess['useValidation'][$this->lic_key] == 0) $useValidation = '';

            $payp_fromcash = ($payp_fromcash == 1) ? "<input type='checkbox' name='payp_fromcash' value='1' checked>" : "<input type='checkbox' name='payp_fromcash' value='1'>";

            $is_pif = ($is_pif == 1) ? "<input type='checkbox' name='is_pif' value='1' checked />" : "<input type='checkbox' name='is_pif' value='1' />";
            $is_pif = "
        <tr>
            <td width='400'><span class='signs_b'>Allow members to pay for other members (PIF feature)</span></td>
            <td width='20'>
                <span title='Allow members to pay for other members (Pay It Forward feature).' class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> $is_pif</td>
        </tr>
            ";
            if ($this->LicenseAccess['is_pif'][$this->lic_key] == 0) $is_pif = '';

            $is_pif_cash = ($is_pif_cash == 1) ? "<input type='checkbox' name='is_pif_cash' value='1' checked />" : "<input type='checkbox' name='is_pif_cash' value='1' />";
            $is_pif_cash = "
        <tr>
            <td width='400'><span class='signs_b'>Allow members to pay for other members using account cash balance (if PIF is activated)</span></td>
            <td width='20'>
                <span title='Allow members to pay for other members  (Pay It Forward feature) using account cash balance.' class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> $is_pif_cash</td>
        </tr>
            ";
            if ($this->LicenseAccess['is_pif_cash'][$this->lic_key] == 0) $is_pif_cash = '';

            if ($is_random == 1) {
                $main_random = "<input type='radio' name='is_random' value='1' checked='checked' /> assign a random sponsor from active members; <br /><input type='radio' name='is_random' value=0>  assign Admin as a sponsor;";
            } else {
                $main_random = "<input type='radio' name='is_random' value='1' /> Assign a random sponsor from active members; <br /><input type='radio' name='is_random' value='0' checked='checked' />  assign Admin as a sponsor;";
            }

            $number_turing = "
        <tr>
            <td width='400'><span class='signs_b'>Number of Symbols in \"Turing Number\":</span></td>
            <td width='20'>
                <span title=\"Number of Symbols in 'Turing Number'. Enter '0' and save changes to disable this feature.\" class='vtip'><img src='./images/question.png'></span>
            </td>
            <td> <input type='text' name='number_turing' value='$number_turing' maxlength='1' style='width:20px;' />&nbsp; <span class='error'>" . $this->GetError("number_turing") . "</span></td>
        </tr>
            ";
            if ($this->LicenseAccess['turing_number'][$this->lic_key] == 0) $number_turing = '';

            $result = $this->db->ExecuteSql("Select * From `currency` Where active=1", "");
            $currency = " <select name='currency'>";
            while ($row = $this->db->FetchInArray($result)) {
                $currency .= "<option value='" . $row['id'] . "' " . ($this->db->GetSetting("currency") == $row['id'] ? 'selected' : '') . " >" . $row['name'] . "</option>";
            }
            $currency .= "</select>";

            $currency = "
            <tr>
                <td width='400'><span class='signs_b'>Currency:</span></td>
                <td width='20'>
                    <span title=\"Choose the symbol of currency you will use for payments\" class=\"vtip\"><img src='./images/question.png'></span>
                </td>
                <td> $currency</td>
            </tr>
            ";
            if ($this->LicenseAccess['currency'][$this->lic_key] == 0) $currency = '';

            if ($this->lic_key != 'FREE') $fee = "<input type='text' name='fee' value='$fee' size='50' maxlength='6'>";
            else $fee = $fee;

            $this->data = array(
                "ACTION_SCRIPT" => $this->pageUrl,
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_SITE_TITLE" => "<input type='text' name='SiteTitle' value='$siteTitle' size='40' maxlength='120'>",
                "MAIN_SITE_TITLE_ERROR" => $this->GetError("SiteTitle"),
                "MAIN_SITE_URL" => "<input type='text' name='SiteUrl' value='$siteUrl' size='40' maxlength='120'>",
                "MAIN_SITE_URL_ERROR" => $this->GetError("SiteUrl"),
                "MAIN_PATH_SITE" => "<input type='text' name='PathSite' value='$pathSite' size='40' maxlength='120'>",
                "MAIN_PATH_SITE_ERROR" => $this->GetError("PathSite"),
                "MAIN_CURRENCY" => $currency,
                "MAIN_CURRENCY_RATE" => $currency_rate,
                "MAIN_PRODUCT" => "<input type='text' name='product' value='$product' size='40' maxlength='120'>",
                "MAIN_PRODUCT_ERROR" => $this->GetError("product"),
                "MAIN_FEE" => $fee,
                "MAIN_FEE_ERROR" => $this->GetError("fee"),
                "MAIN_CASH_OUT" => "<input type='text' name='Mincashout' value='$mincashout' size='50' maxlength='6'>",
                "MAIN_CASH_OUT_ERROR" => $this->GetError("mincashout"),
                "MAIN_TYPE" => $type,
                "SPONSOR_AMOUNT" => "<input type='text' name='sponsor_amount' value='$sponsor_amount' maxlength='6' style='width:50px;'>",
                "SPONSOR_QUANT" => "<input type='text' name='sponsor_quant' value='$sponsor_quant' maxlength='6' style='width:50px;'>",
                "REPLICA" => $replica,
                "QUANT_REPLICA" => $QUANT_REPLICA,
                "QUANT_REPLICA_ERROR" => $this->GetError("quant_replica"),
                "MAIN_SECURE" => $useSecureMembers,
                "MAIN_AUTO" => $useAutoresponder,
                "MAIN_ESHOP" => $useEshop,
                "MAIN_VALIDATION" => $useValidation,

                "QUANT_TEXTADDS" => "<input type='text' name='quant_textadds' value='$quant_textadds' maxlength='2' style='width:50px;'>",
                "QUANT_TEXTADDS_ERROR" => $this->GetError("quant_textadds"),

                "QUANT_TEXTADDS_SHOW" => "<input type='text' name='quant_textadds_show' value='$quant_textadds_show' maxlength='2' style='width:50px;'>",
                "QUANT_TEXTADDS_SHOW_ERROR" => $this->GetError("quant_textadds_show"),

                "QUANT_TEXTADDS_SHOW_M" => "<input type='text' name='quant_textadds_show_m' value='$quant_textadds_show_m' maxlength='2' style='width:50px;'>",
                "QUANT_TEXTADDS_SHOW_M_ERROR" => $this->GetError("quant_textadds_show_m"),

                "NUMBER_TURING" => $number_turing,
                "NUMBER_TURING_ERROR" => $this->GetError("number_turing"),

                "MAIN_PAYPFROMCASH" => $payp_fromcash,
                "IS_PIF" => $is_pif,
                "IS_PIF_CASH" => $is_pif_cash,
                "MAIN_RANDOM" => $main_random,
            );
        } else {
            if (substr($siteUrl, -1) != "/") $siteUrl = $siteUrl . "/";
            if (substr($pathSite, -1) != "/") $pathSite = $pathSite . "/";
            $this->db->SetSetting("SiteTitle", $siteTitle);
            $this->db->SetSetting("SiteUrl", $siteUrl);
            $this->db->SetSetting("product", $product);
            $this->db->SetSetting("currency", $currency);
            $this->db->SetSetting("currency_rate", $currency_rate);
            $this->db->SetSetting("PathSite", $pathSite);
            $this->db->SetSetting("MinCashOut", $mincashout);
            if ($this->lic_key != 'FREE') $this->db->SetSetting("fee", $fee);
            $this->db->SetSetting("sponsor_amount", $sponsor_amount);
            $this->db->SetSetting("sponsor_quant", $sponsor_quant);
            if ($this->LicenseAccess['is_replica'][$this->lic_key] == 1) $this->db->SetSetting("is_replica", $is_replica);
            if ($this->LicenseAccess['ptools'][$this->lic_key] == 1) $this->db->SetSetting("useBanners", $ptools);
            $this->db->SetSetting("useSecureMembers", $useSecureMembers);
            $this->db->SetSetting("useAutoresponder", $useAutoresponder);
            if ($this->LicenseAccess['quant_replica'][$this->lic_key] == 1) $this->db->SetSetting("quant_replica", $quant_replica);
            if ($this->LicenseAccess['categories'][$this->lic_key] == 1) $this->db->SetSetting("useEshop", $useEshop);
            if ($this->LicenseAccess['useValidation'][$this->lic_key] == 1) $this->db->SetSetting("useValidation", $useValidation);
            $this->db->SetSetting("quant_textadds", $quant_textadds);
            $this->db->SetSetting("quant_textadds_show", $quant_textadds_show);
            $this->db->SetSetting("quant_textadds_show_m", $quant_textadds_show_m);
            $this->db->SetSetting("number_turing", $number_turing);
            $this->db->SetSetting("payp_fromcash", $payp_fromcash);
            if ($this->LicenseAccess['is_pif'][$this->lic_key] == 1) $this->db->SetSetting("is_pif", $is_pif);
            if ($this->LicenseAccess['is_pif_cash'][$this->lic_key] == 1) $this->db->SetSetting("is_pif_cash", $is_pif_cash);
            $this->db->SetSetting("is_random", $is_random);

            $mode_in_db = $this->db->GetSetting("cycling");

            $this->db->SetSetting("ReferrerUrl", $REFERRER_URL);

            $this->db->SetSetting("COMMISSIONS_VALUE", $COMMISSIONS_VALUE);
            $this->db->SetSetting("SPONSOR_VALUE", $SPONSOR_VALUE);
            $this->db->SetSetting("WITHDRAWAL_VALUE", $WITHDRAWAL_VALUE);


            $this->db->SetSetting("matching_bonus", ($matching_bonus == 'on' ? 1 : 0));
            $this->db->SetSetting("matching_bonus_value", $matching_bonus_value);
            if ($matching_bonus == 'on') $this->db->SetSetting("after_launch", 0);

            if ($mode_in_db <> $mode) {
                $date = time();
                $this->db->ExecuteSql("TRUNCATE TABLE `payins`");
                $this->db->ExecuteSql("TRUNCATE TABLE `cash_out`");
                $this->db->ExecuteSql("TRUNCATE TABLE `cash`");
                $this->db->ExecuteSql("TRUNCATE TABLE `fees`");
                $this->db->ExecuteSql("TRUNCATE TABLE `shop_fees`");
                $this->db->ExecuteSql("TRUNCATE TABLE `types`");
                $this->db->ExecuteSql("TRUNCATE TABLE `sponsor_bonus`");
                $this->db->ExecuteSql("TRUNCATE TABLE `members`");
                $this->db->ExecuteSql("TRUNCATE TABLE `matrix`");
                $this->db->ExecuteSql("TRUNCATE TABLE `logs`");
                $this->db->ExecuteSql("TRUNCATE TABLE `text_ads`");
                $this->db->SetSetting("product", "Product");

                $this->db->ExecuteSql("TRUNCATE TABLE `places`");
                $this->db->ExecuteSql("TRUNCATE TABLE `matrices_completed`");

                $this->db->ExecuteSql("Insert Into `members` (member_id, username, enroller_id, passwd, email, first_name, last_name, reg_date, m_level, is_active, is_dead) Values (1, 'admin', 0, '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', 'Admin', 'Admin', '$date', 1, 1, 0)");

                if ($mode == 1) {
                    $this->db->ExecuteSql("Insert Into `places` (member_id, referrer_place_id, m_level, z_date, reentry) Values (1, 0, 1, UNIX_TIMESTAMP(), 1)");
                    $this->db->ExecuteSql("Insert Into `types` Values (1, 1, 'First', '0.00', '10.00', '0.00', 2, 2)");
                } else {
                    $this->db->ExecuteSql("Insert Into `matrix` Values (1, 1, 0, 0, 0, 0, 1, 0, '$date', 0)");
                    $this->db->ExecuteSql("Insert Into `types` Values (1, 1, 'Free', '0.00', '0.00', '0.00', 0, 0)");
                }
                $this->db->SetSetting("cycling", $mode);
            }
            $this->Redirect($this->pageUrl . "?ec=done");
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("settings");

$zPage->Render();

?>
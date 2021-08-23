<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_member.php");
require_once("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage($object)
    {
        GLOBAL $dict;
        $this->mainTemplate = "./templates/myaccount.tpl";
        $this->pageTitle = $dict['MyAcc_pageTitle'];
        $this->pageHeader = $dict['MyAcc_pageTitle'];

        XPage::XPage($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list()
    {
        GLOBAL $dict;
        $member_id = $this->member_id;
        $siteUrl = $this->db->GetSetting("SiteUrl");
        if ($_SERVER['QUERY_STRING'] == '' || isset($_GET['overview'])) $this->data['OVERVIEW'] = ' active';
        else $this->data['OVERVIEW'] = '';
        if (isset($_GET['accesssettings'])) $this->data['ACCESSSETTINGS'] = ' active';
        else $this->data['ACCESSSETTINGS'] = '';
        if (isset($_GET['addresssettings'])) $this->data['ADDRESSSETTINGS'] = ' active';
        else $this->data['ADDRESSSETTINGS'] = '';
        if (isset($_GET['paymentsettings'])) $this->data['PAYMENTSETTONGS'] = ' active';
        else $this->data['PAYMENTSETTONGS'] = '';

        $account_tabs = '
                <li role="presentation" class="' . $this->data['OVERVIEW'] . '"><a href="#Overview" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_Overview'] . '</a></li>
                <li role="presentation" class="' . $this->data['ACCESSSETTINGS'] . '"><a href="#Access" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_Accesssettings'] . '</a></li>
                <li role="presentation" class="' . $this->data['ADDRESSSETTINGS'] . '"><a href="#Address" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_AddressSettings'] . '</a></li>
                <li role="presentation" class="' . $this->data['PAYMENTSETTONGS'] . '"><a href="#Payment" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_PaymentSettings'] . '</a></li>
            ';

        $message = "";
        $ec = $this->GetGP("ec", "");
        if ($ec == "done") $message = $dict['MyAcc_Text2'];
        if ($ec == "sent") $message = $dict['MyAcc_Text3'];

        $row = $this->db->GetEntry("Select * From `members` Where member_id='$member_id'");


        $secureMode = ($row['ip_check'] == 1) ? "checked" : "";
        $secureMode = "<input type='checkbox' name='secureMode' value='1' $secureMode>";


        $is_replica = ($row['is_replica'] == 1) ? "checked" : "";
        $is_replica = "<input type='checkbox' name='is_replica' value='1' $is_replica>";

        $is_a_replica = ($row['is_a_replica'] == 1) ? $dict['MyAcc_Authorized'] : $dict['MyAcc_NotAuthorized'];

        $replica = $row['replica'];
        $replica = "<input type='text' name='replica' value='$replica' style='width:100px;' maxlength='15'>";

        $replica_admin = $this->db->GetSetting("is_replica", 0);
        $useSecureMembers = $this->db->GetSetting("useSecureMembers", 0);

        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_CONFIRM" => $message,
            "ACCOUNT_SECURE" => $secureMode,

            "ACCOUNT_TABS" => $account_tabs,

            "ACCOUNT_IS_REPLICA" => $is_replica,
            "ACCOUNT_IS_A_REPLICA" => $is_a_replica,
            "ACCOUNT_REPLICA_URL" => $replica,
            "ACCOUNT_SITE_URL" => $siteUrl,
        );
        if ($replica_admin == 1) {
            $this->data ['REPLICA'][] = array(
                "_" => "_"
            );
        }
        if ($useSecureMembers == 1) {
            $this->data ['SECURE'][] = array(
                "_" => "_"
            );
        }

        if ($_SERVER['QUERY_STRING'] == '' || isset($_GET['overview'])) $this->data['OVERVIEW'] = ' active';
        if (isset($_GET['accesssettings'])) $this->data['ACCESSSETTINGS'] = ' active';
        if (isset($_GET['addresssettings'])) $this->data['ADDRESSSETTINGS'] = ' active';
        if (isset($_GET['paymentsettings'])) $this->data['PAYMENTSETTONGS'] = ' active';

// OVERVIEW --------------------
//		if ($_SERVER['QUERY_STRING']=='' || isset($_GET['overview'])) {
        $thisSiteUrl = $this->db->GetSetting("SiteUrl");
        $row = $this->db->GetEntry("Select * From `members` Where member_id='$member_id'", "");
        $name = $this->dec($row ['first_name']) . " " . $this->dec($row ['last_name']);
        $reg_date = $row ['reg_date'];
        $last_access = $row ['last_access'];
        $quant_pay = $row ['quant_pay'];

        $level = $this->db->GetOne("Select m_level From `members` Where member_id='$member_id'", 1);

        $ReferrerUrl = $this->db->GetSetting("ReferrerUrl");
        $ref_id = $this->db->GetOne("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);

        $cycling = $this->db->GetSetting("cycling", 0);
        $status = "";
        if ($cycling == 1) {
            if ($level == 0) {
                $ref_link = $dict['MyAcc_reflink'];
                $upgrade_link = "[ <a href='payment.php'>{$dict['MyAcc_Upgradelevel']}</a> ]";

                $landing_pages_title = "";
                $landing_pages = "";

            } else {
                $ref_link = '<input type="text" class="form-control" placeholder="" value="' . $thisSiteUrl . "?ref=" . $ref_id . '" >';
                $upgrade_link = $this->dec($this->db->GetOne("Select title From `types` Where order_index='$level'"));

                $landing_pages = "";

                $countLands = $this->db->GetOne("Select COUNT(*) From `lands` Where `is_active`='1'", 0);
                $landing_pages_title = ($countLands > 0) ? $dict['MyAcc_LandingPages'] : "";

                $result = $this->db->ExecuteSql("Select * From `lands` Where `is_active`='1' Order By z_date Asc");
                while ($row1 = $this->db->FetchInArray($result)) {
                    $id = $row1['land_id'];
                    $landing_pages .= ($countLands == 1) ? "<input type='text' name='land_id' value='" . $thisSiteUrl . "land.php?ref=" . $ref_id . "' class='form-control'> <a href='" . $thisSiteUrl . "land.php?ref=" . $ref_id . "' target='_blank' />View Page</a> <br />" : "<input type='text' name='land_id' value='" . $thisSiteUrl . "land.php?id=" . $id . "&ref=" . $ref_id . "' class='form-control'> <a href='" . $thisSiteUrl . "land.php?id=" . $id . "&ref=" . $ref_id . "' target='_blank' />View Page</a> <br />";
                }
                $this->db->FreeSqlResult($result);
            }

            $downlines = $acc_d_title = "";
        } else {
            $ref_link = '<input type="text" class="form-control" placeholder="" value="' . $thisSiteUrl . "?ref=" . $ref_id . '" >';
            $count_high_levels = $this->db->GetOne("Select Count(*) From `types` Where order_index>'$level'", 0);
            $add = ($count_high_levels > 0) ? " [ <a href='payment.php'>{$dict['MyAcc_Upgradelevel']}</a> ]" : "";
            $upgrade_link = $this->dec($this->db->GetOne("Select title From `types` Where order_index='$level'")) . $add;

            if ( isPreLaunch() ) {
                $upgrade_link = $this->dec($this->db->GetOne("Select title From `types` Where order_index='$level'")) . "[ <a href='payment_hold.php'>{$dict['MyAcc_Upgradelevel']}</a> ]";
            }

            $thisTime = time();
            $turn_date = $this->db->GetSetting("PaymentModeDate");
            $payPeriod = $this->db->GetSetting("payPeriod");
            $warnPeriod = $this->db->GetSetting("warnPeriod");
            $monthPeriod = $this->db->GetSetting("monthPeriod");

            $tempore = max($reg_date, $turn_date) + $quant_pay * $monthPeriod * 24 * 3600;
            $tempore_to = $tempore + $payPeriod * 24 * 3600;
            $tempore_del = $tempore_to + $warnPeriod * 24 * 3600;
            if ($thisTime < $tempore) $status = "Active.<br>Paid until " . date("d M Y H:i", $tempore);
            if ($thisTime > $tempore and $thisTime < $tempore_to) $status = "{$dict['MyAcc_Text4']}" . date("d M Y H:i", $tempore_to) . " {$dict['MyAcc_Text5']}";
            if ($thisTime > $tempore_to) $status = "{$dict['MyAcc_Text6']}" . date("d M Y H:i", $tempore_del) . " {$dict['MyAcc_Text7']}";

            $status = "<tr><td valign='top'>{$dict['MyAcc_YourStatus']}:</td><td>$status</td></tr>";

            if (isPreLaunch()) $status = '';
            
            $downline = array();
            $downline = getNumberDownlines($member_id, $downline);
            $downlines = Count($downline);

            $landing_pages = "";
            $countLands = $this->db->GetOne("Select COUNT(*) From `lands` Where `is_active`='1'", 0);
            $landing_pages_title = ($countLands > 0) ? "{$dict['MyAcc_LandingPages']}" : "";
            $result = $this->db->ExecuteSql("Select * From `lands` Where `is_active`='1' Order By `z_date` Asc");
            while ($row1 = $this->db->FetchInArray($result)) {
                $id = $row1['land_id'];
                $landing_pages .= ($countLands == 1) ? "<input type='text' name='land_id' value='" . $thisSiteUrl . "land.php?ref=" . $ref_id . "' class='form-control'> <a href='" . $thisSiteUrl . "land.php?ref=" . $ref_id . "' target='_blank' />View Page</a> <br />" : "<input type='text' name='land_id' value='" . $thisSiteUrl . "land.php?id=" . $id . "&ref=" . $ref_id . "' class='form-control'> <a href='" . $thisSiteUrl . "land.php?id=" . $id . "&ref=" . $ref_id . "' target='_blank' />View Page</a> <br />";
            }
            $this->db->FreeSqlResult($result);

            $acc_d_title = $dict['MyAcc_Downlinemembers'];
        }

        if ($landing_pages == "") $landing_pages_title = "";

        $sponsors = $this->db->GetOne("Select Count(*) From `members` Where enroller_id='$member_id' And is_dead=0", 0);
        $cash = $this->db->GetOne("Select SUM(amount) From `cash` Where to_id='$member_id'", "0.00");
        $cash = sprintf("%01.2f", $cash);

        $ACCOUNT_EARNED = (isPreLaunch()?'':['_'=>'_']);

        $this->data += array(
            "ACCOUNT_REGISTRATION" => date('d M Y H:i', $reg_date),
            "ACCOUNT_LAST_ACCESS" => date('d M Y H:i', $last_access),
            "ACCOUNT_ID" => $member_id,
            "ACCOUNT_LINK" => $ref_link,
            "ACCOUNT_ENROLLER" => ($row ['enroller_id'] > 0) ? $row ['enroller_id'] . " <a href='contact.php?e=" . $row ['enroller_id'] . "'><img src='./images/mail.png' border='0' ></a>" : $dict['MyAcc_Text8'],
            "ACCOUNT_UPGRADE" => $upgrade_link,
            "ACCOUNT_SPONSORS" => $sponsors,
            "ACCOUNT_STATUS" => $status,
            "ACCOUNT_DOWNLINES" => $downlines,
            "DOWNLINES_LINK" => ($downlines > 0) ? "<a href='contact.php?s=0'><img src='./images/mail.png' border='0' ></a>" : "",

            "ACCOUNT_LANDS_TITLE" => $landing_pages_title,
            "ACCOUNT_LANDS" => $landing_pages,
            "ACCOUNT_DOWNLINES_TITLE" => $acc_d_title,
            "ACCOUNT_CASH" => $cash,
            "ACCOUNT_EARNED" => $ACCOUNT_EARNED,
        );
//		}
// =============================

// ACCESSSETTINGS --------------------
//		if (isset($_GET['accesssettings'])){
        $lastName = $row['last_name'];
        $lastName = "<input type='text' name='lastName' value='$lastName' style='width:200px;' maxlength='50'>";

        $firstName = $row['first_name'];
        $firstName = "<input type='text' name='firstName' value='$firstName' style='width:200px;' maxlength='50'>";

        $email = $row['email'];
        $email = "<input type='text' name='email' value='$email' style='width:200px;' maxlength='120'>";

        $passwd = $dict['MyAcc_Coded'];

        $userName = $row['username'];

        $avatar = '';
        $physical_path = $this->db->GetSetting("PathSite");
        $filename = "avatar_" . $member_id . '.jpg';
        if (file_exists($physical_path . "data/avatar/" . $filename)) {
            $delLink = "<a href='{$this->pageUrl}?ocd=delavatar' onClick=\"return confirm ('{$dict['PT_mess4']}');\"><img src='./images/trash.png' border='0' alt='Delete avatar' title='Delete avatar' /></a>";
            $avatar = '<img src="/data/avatar/' . $filename . '" alt="prof" /> ' . $delLink;
        } else $avatar = "<input type='file'  name='avatar' value='' style='width: 320px;' />";

        $this->data += array(
            "OVERVIEW_LASTNAME" => $lastName,
            "OVERVIEW_FIRSTNAME" => $firstName,
            "OVERVIEW_USERNAME" => $userName,
            "OVERVIEW_PASSWD" => $passwd,
            "OVERVIEW_EMAIL" => $email,
            "OVERVIEW_AVATAR" => $avatar
        );
//		}
// =============================

// ADDRESSSETTINGS --------------------
//		if (isset($_GET['addresssettings'])) {
        $street = $row['street'];
        $street = "<input type='text' name='street' value='$street' style='width:200px;' maxlength='50'>";

        $city = $row['city'];
        $city = "<input type='text' name='city' value='$city' style='width:200px;' maxlength='50'>";

        $state = $row['state'];
        $state = "<input type='text' name='state' value='$state' style='width:200px;' maxlength='50'>";

        $country = $row['country'];
        $country = "<input type='text' name='country' value='$country' style='width:200px;' maxlength='50'>";

        $postal = $row['postal'];
        $postal = "<input type='text' name='postal' value='$postal' style='width:200px;' maxlength='50'>";

        $phone = $row['phone'];
        $phone = "<input type='text' name='phone' value='$phone' style='width:200px;' maxlength='50'>";

        $this->data += array(
            "ACCOUNT_STREET" => $street,
            "ACCOUNT_CITY" => $city,
            "ACCOUNT_STATE" => $state,
            "ACCOUNT_COUNTRY" => $country,
            "ACCOUNT_POSTAL" => $postal,
            "ACCOUNT_PHONE" => $phone,
        );
//		}
// =============================

// PAYMENTSETTONGS --------------------
//		if (isset($_GET['paymentsettongs'])) {
        $processor = $this->selectProcessor($row['processor']);

        $account_id = $row['account_id'];
        $account_id = "<input type='text' name='account_id' value='$account_id' style='width:200px;' maxlength='150'>";

        $this->data += array(
            "ACCOUNT_PROCESSOR" => $processor,
            "ACCOUNT_ACCOUNT_ID" => $account_id,
        );
//		}
// =============================

    }

    //--------------------------------------------------------------------------
    function selectProcessor($value = 0)
    {
        GLOBAL $dict;
        $toRet = "<select name='processor' style='width:200px;'> \r\n";
        $result = $this->db->ExecuteSql("Select processor_id, name From `processors` Where is_active=1 Order By name");

        $selected = ($value == 0) ? "selected" : "";
        $toRet .= "<option value='0' $selected>{$dict['MyAcc_Selectprocessor']}</option>";

        while ($row = $this->db->FetchInArray($result)) {
            $selected = ($row['processor_id'] == $value) ? "selected" : "";
            $toRet .= "<option value='" . $row['processor_id'] . "' $selected>" . $row['name'] . "</option>";
        }

        return $toRet . "</select>\r\n";
    }

    //--------------------------------------------------------------------------
    function ocd_update()
    {
        GLOBAL $dict;
        $member_id = $this->member_id;
        if ($_SERVER['QUERY_STRING'] == '' || isset($_GET['overview'])) $this->data['OVERVIEW'] = ' active';
        else $this->data['OVERVIEW'] = '';
        if (isset($_GET['accesssettings'])) $this->data['ACCESSSETTINGS'] = ' active';
        else $this->data['ACCESSSETTINGS'] = '';
        if (isset($_GET['addresssettings'])) $this->data['ADDRESSSETTINGS'] = ' active';
        else $this->data['ADDRESSSETTINGS'] = '';
        if (isset($_GET['paymentsettings'])) $this->data['PAYMENTSETTONGS'] = ' active';
        else $this->data['PAYMENTSETTONGS'] = '';

        $account_tabs = '
                <li role="presentation" class="' . $this->data['OVERVIEW'] . '"><a href="#Overview" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_Overview'] . '</a></li>
                <li role="presentation" class="' . $this->data['ACCESSSETTINGS'] . '"><a href="#Access" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_Accesssettings'] . '</a></li>
                <li role="presentation" class="' . $this->data['ADDRESSSETTINGS'] . '"><a href="#Address" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_AddressSettings'] . '</a></li>
                <li role="presentation" class="' . $this->data['PAYMENTSETTONGS'] . '"><a href="#Payment" aria-controls="profile" role="tab" data-toggle="tab">' . $dict['MyAcc_PaymentSettings'] . '</a></li>
            ';

        $firstName = '';
        $lastName = '';
        $email = '';
        $processor = '';
        $account_id = '';
        $street = '';
        $city = '';
        $state = '';
        $country = '';
        $postal = '';
        $phone = '';

        if ($this->GetGP('ocd_type') == 'ADDRESSSETTINGS') {
            $street = $this->enc($this->GetValidGP("street", $dict['MyAcc_Address'], VALIDATE_NOT_EMPTY));
            $city = $this->enc($this->GetValidGP("city", $dict['MyAcc_City'], VALIDATE_NOT_EMPTY));
            $state = $this->enc($this->GetValidGP("state", $dict['MyAcc_State'], VALIDATE_NOT_EMPTY));

            $country = $this->enc($this->GetValidGP("country", $dict['MyAcc_Country'], VALIDATE_NOT_EMPTY));
            $postal = $this->enc($this->GetValidGP("postal", $dict['MyAcc_PostalCode'], VALIDATE_NOT_EMPTY));
            $phone = $this->enc($this->GetValidGP("phone", $dict['MyAcc_Phone'], VALIDATE_NOT_EMPTY));

        }

        if ($this->GetGP('ocd_type') == 'ACCESSSETTINGS') {
            $firstName = $this->enc($this->GetValidGP("firstName", $dict['MyAcc_FirstName'], VALIDATE_NOT_EMPTY));
            $lastName = $this->enc($this->GetValidGP("lastName", $dict['MyAcc_LastName'], VALIDATE_NOT_EMPTY));
            $email = $this->GetValidGP("email", $dict['MyAcc_EmailAddress'], VALIDATE_EMAIL);

            if ($this->errors['err_count'] == 0) {
                if (array_key_exists("avatar", $_FILES) and $_FILES['avatar']['error'] < 3) {
                    $types = $_FILES['avatar']['type'];
                    $types_array = explode("/", $types);
                    if ($types_array [0] != "image") $this->SetError("avatar", $dict['PT_errorphoto1']);
                    if (strpos($_FILES['avatar']['name'], 'php') !== false) $this->SetError("avatar", $dict['PT_errorphoto1']);
                    if (strpos($_FILES['avatar']['name'], 'jpg') === false) $this->SetError("avatar", 'Only JPG file');
                }
            }

        }

        if ($this->GetGP('ocd_type') == 'PAYMENTSETTONGS') {
            $processor = $this->GetID("processor");
            $account_id = $this->enc($this->GetValidGP("account_id", $dict['MyAcc_AccountID'], VALIDATE_NOT_EMPTY));
        }

        $secureMode = $this->GetID("secureMode");

        $replica_admin = $this->db->GetSetting("is_replica", 0);
        $useSecureMembers = $this->db->GetSetting("useSecureMembers", 0);

        $replica = $this->GetGP("replica", "");
        $is_replica = $this->GetGP("is_replica", 0);

        if ($replica != "" And $replica_admin == 1) {
            $replica = $this->enc($this->GetValidGP("replica", $dict['MyAcc_UrlofMySite'], VALIDATE_REPLICA));
            if ($this->errors['err_count'] == 0) {
                $count = $this->db->GetOne("Select Count(*) From `members` Where replica='$replica' And member_id<>'$member_id'", 0);
                if ($count > 0) $this->SetError("replica", $dict['MyAcc_Text9']);
            }
        }


        if ($this->errors['err_count'] == 0) {
//            $count = $this->db->GetOne ("Select Count(*) From `members` Where username='$userName' And member_id<>'$member_id'", 0);
//            if ($count > 0) $this->SetError ("userName", "This Username already exists. Please choose another.");

            $count = $this->db->GetOne("Select Count(*) From `members` Where email='$email' And member_id<>'$member_id'", 0);
            if ($count > 0) $this->SetError("email", $dict['MyAcc_Text10']);

            if ($this->GetGP('ocd_type') == 'PAYMENTSETTONGS') if ($processor == 0) $this->SetError("processor", $dict['MyAcc_Text11']);
        }
//debug($this->errors);
        if ($this->errors['err_count'] > 0) {
            $secureMode = ($secureMode == 1) ? "checked" : "";

            $siteUrl = $this->db->GetSetting("SiteUrl");
            $is_replica = ($is_replica == 1) ? "checked" : "";
            $is_a_replica = $this->db->GetOne("Select is_a_replica From `members` Where member_id='$member_id'", 0);
            $is_a_replica = ($is_a_replica == 1) ? $dict['MyAcc_Authorized'] : $dict['MyAcc_NotAuthorized'];

            $username = $this->db->GetOne("Select username From `members` Where member_id='$member_id'", 0);

            $avatar = '';
            $physical_path = $this->db->GetSetting("PathSite");
            $filename = "avatar_" . $member_id . '.jpg';
            if (file_exists($physical_path . "data/avatar/" . $filename)) {
                $delLink = "<a href='{$this->pageUrl}?ocd=delavatar' onClick=\"return confirm ('{$dict['PT_mess4']}');\"><img src='./images/trash.gif' border='0' alt='Delete avatar' title='Delete avatar' /></a>";
                $avatar = '<img src="/data/avatar/' . $filename . '" alt="prof" /> ' . $delLink;
            } else $avatar = "<input type='file'  name='avatar' value='' style='width: 320px;' />";

            $this->data = array(
                "ACCOUNT_TABS" => $account_tabs,
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                "OVERVIEW_LASTNAME" => "<input type='text' name='lastName' value='$lastName' maxlength='50' style='width: 200px;'>",
                "OVERVIEW_LASTNAME_ERROR" => $this->GetError("lastName"),
                "OVERVIEW_FIRSTNAME" => "<input type='text' name='firstName' value='" . $firstName . "' maxlength='50' style='width: 200px;'>",
                "OVERVIEW_FIRSTNAME_ERROR" => $this->GetError("firstName"),
                "OVERVIEW_USERNAME" => $username,
                "OVERVIEW_EMAIL" => "<input type='text' name='email' value='$email' maxlength='120' style='width: 200px;'>",
                "OVERVIEW_EMAIL_ERROR" => $this->GetError("email"),
                "OVERVIEW_PASSWD" => "Coded",
                "ACCOUNT_PROCESSOR" => $this->selectProcessor($processor),
                "ACCOUNT_PROCESSOR_ERROR" => $this->GetError("processor"),

                "ACCOUNT_ACCOUNT_ID" => "<input type='text' name='account_id' value='" . $account_id . "' maxlength='150' style='width: 200px;'>",
                "ACCOUNT_ACCOUNT_ID_ERROR" => $this->GetError("account_id"),

                "ACCOUNT_SECURE" => "<input type='checkbox' name='secureMode' value='1' $secureMode>",

                "ACCOUNT_STREET" => "<input type='text' name='street' value='$street' style='width:200px;' maxlength='50'>",
                "ACCOUNT_STREET_ERROR" => $this->GetError("street"),
                "ACCOUNT_CITY" => "<input type='text' name='city' value='$city' style='width:200px;' maxlength='50'>",
                "ACCOUNT_CITY_ERROR" => $this->GetError("city"),
                "ACCOUNT_STATE" => "<input type='text' name='state' value='$state' style='width:200px;' maxlength='50'>",
                "ACCOUNT_STATE_ERROR" => $this->GetError("state"),
                "ACCOUNT_COUNTRY" => "<input type='text' name='country' value='$country' style='width:200px;' maxlength='50'>",
                "ACCOUNT_COUNTRY_ERROR" => $this->GetError("country"),
                "ACCOUNT_POSTAL" => "<input type='text' name='postal' value='$postal' style='width:200px;' maxlength='50'>",
                "ACCOUNT_POSTAL_ERROR" => $this->GetError("postal"),
                "ACCOUNT_PHONE" => "<input type='text' name='phone' value='$phone' style='width:200px;' maxlength='50'>",
                "ACCOUNT_PHONE_ERROR" => $this->GetError("phone"),

                "ACCOUNT_IS_REPLICA" => "<input type='checkbox' name='is_replica' value='1' $is_replica>",
                "ACCOUNT_IS_A_REPLICA" => $is_a_replica,
                "ACCOUNT_REPLICA_URL" => "<input type='text' name='replica' value='$replica' style='width:100px;' maxlength='15'>",
                "ACCOUNT_REPLICA_ERROR" => $this->GetError("replica"),
                "ACCOUNT_SITE_URL" => $siteUrl,

                "OVERVIEW_AVATAR" => $avatar,
                "OVERVIEW_AVATAR_ERROR" => $this->GetError("avatar"),

            );
            if ($replica_admin == 1) {
                $this->data ['REPLICA'][] = array(
                    "_" => "_"
                );
            }

            if ($useSecureMembers == 1) {
                $this->data ['SECURE'][] = array(
                    "_" => "_"
                );
            }

            if ($this->GetGP('ocd_type') == 'OVERVIEW') {
                $this->data ['OVERVIEW'][] = array(
                    "_" => "_"
                );
                $this->data['OVERVIEW'] = ' active';
            }
            if ($this->GetGP('ocd_type') == 'ADDRESSSETTINGS') {
                $this->data ['ADDRESSSETTINGS'][] = array(
                    "_" => "_"
                );
                $this->data['ADDRESSSETTINGS'] = ' active';
            }
            if ($this->GetGP('ocd_type') == 'ACCESSSETTINGS') {
                $this->data ['ACCESSSETTINGS'][] = array(
                    "_" => "_"
                );
                $this->data['ACCESSSETTINGS'] = ' active';
            }
            if ($this->GetGP('ocd_type') == 'PAYMENTSETTONGS') {
                $this->data ['PAYMENTSETTONGS'][] = array(
                    "_" => "_"
                );
                $this->data['PAYMENTSETTONGS'] = ' active';
            }

        } else {
            $last_email = $this->db->GetOne("Select email From `members` Where member_id='$member_id'");


            if ($useSecureMembers == 1) {
                if ($secureMode == 1) {
                    $ip_address = $this->GetServer("REMOTE_ADDR", "unknown");
                    $this->db->ExecuteSql("Update `members` Set ip_check='$secureMode', ip_address='$ip_address' Where member_id='$member_id'");
                } else {
                    $this->db->ExecuteSql("Update `members` Set ip_check='0', ip_address='', pin_code='' Where member_id='$member_id'");
                }
            }

            if ($this->GetGP('ocd_type') == 'ADDRESSSETTINGS') {
                $this->db->ExecuteSql("Update `members` Set street='$street', city='$city', state='$state', country='$country', postal='$postal', phone='$phone' Where member_id='$member_id'");
                $this->Redirect($this->pageUrl . "?addresssettings&ec=done");
            }

            if ($this->GetGP('ocd_type') == 'ACCESSSETTINGS') {
                $this->db->ExecuteSql("Update `members` Set first_name='$firstName', last_name='$lastName', email='$email' Where member_id='$member_id'");
                if (array_key_exists("avatar", $_FILES) and $_FILES['avatar']['error'] < 3) {
                    $oldname = $_FILES['avatar']['name'];
                    $tmp_name = $_FILES['avatar']['tmp_name'];
                    $ext = getExtension($oldname, "jpg");
                    $new_name = "avatar_" . $member_id . "." . $ext;
                    if (is_uploaded_file($tmp_name)) {
                        $physical_path = $this->db->GetSetting("PathSite");
                        move_uploaded_file($tmp_name, $physical_path . "data/avatar/" . $new_name);
                        makeThumbnail($physical_path . "data/avatar/" . $new_name, 2);
                    }
                }
                $this->Redirect($this->pageUrl . "?accesssettings&ec=done");
            }

            if ($this->GetGP('ocd_type') == 'PAYMENTSETTONGS') {
                $this->db->ExecuteSql("Update `members` Set processor='$processor', account_id='$account_id' Where member_id='$member_id'");
                $this->Redirect($this->pageUrl . "?paymentsettings&ec=done");
            }


//            $this->db->ExecuteSql ("Update `members` Set first_name='$firstName', last_name='$lastName', email='$email', processor='$processor', account_id='$account_id', street='$street', city='$city', state='$state', country='$country', postal='$postal', phone='$phone' Where member_id='$member_id'");

            if ($replica_admin == 1) $this->db->ExecuteSql("Update `members` Set replica='$replica', is_replica='$is_replica' Where member_id='$member_id'");

            /*
                        $siteTitle = $this->db->GetSetting ("SiteTitle");
                        $emailSubject = "Account info from ".$siteTitle;
                        $message = "Your account info has been changed!\r\n\r\n";
                        $message = "Your current account:\r\n";
                        $message .= "first name=$firstName, last name=$lastName, email=$email, username=$userName";
                        sendMail ($email, $emailSubject, $message, $this->emailHeader);
                        if ($email != $last_email) sendMail ($last_email, $emailSubject, $message, $this->emailHeader);
            */
            $this->Redirect($this->pageUrl . "?ec=done");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_delavatar()
    {
        $member_id = $this->member_id;
        $physical_path = $this->db->GetSetting("PathSite");
        $filename = 'avatar_' . $member_id . '.jpg';
        if (file_exists($physical_path . "data/avatar/" . $filename)) unlink($physical_path . "data/avatar/" . $filename);
        $this->Redirect($this->pageUrl . "?accesssettings&ec=done");
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("account");

$zPage->Render();

?>


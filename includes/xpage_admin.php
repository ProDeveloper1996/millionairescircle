<?php

@ini_set ("max_execution_time", 36000);
@set_time_limit (36000);
@ini_set ("display_errors", "1");
@error_reporting (E_ALL);
//@set_magic_quotes_runtime (0);

//------------------------------------------------------------------------------
//define ("ASERVER_COM", "http://aserver.rollersoft.com/activation_check.php");
define ("ASERVER_COM", "http://as.mirofit.com/activation_check.php");

define ("VALIDATE_NOT_EMPTY", 1);
define ("VALIDATE_USERNAME", 2);
define ("VALIDATE_PASSWORD", 3);
define ("VALIDATE_PASS_CONFIRM", 4);
define ("VALIDATE_EMAIL", 5);
define ("VALIDATE_INT_POSITIVE", 6);
define ("VALIDATE_FLOAT_POSITIVE", 7);
define ("VALIDATE_CHECKBOX", 8);
define ("VALIDATE_NUMERIC", 9);
define ("VALIDATE_NUMERIC_POSITIVE", 10);

//------------------------------------------------------------------------------

define ("FORM_EMPTY", 1);
define ("FORM_FROM_DB", 2);
define ("FORM_FROM_GP", 3);

//------------------------------------------------------------------------------

$db = new XDB ();

require_once ($db->GetSetting("PathSite")."lang/".LANG."/system.php");

class XPage
{
    var $db = null;

    var $LicenseAccess = array();

    var $mainTemplate;
    var $headerTemplate = "./templates/header.tpl";
    var $footerTemplate = "./templates/footer.tpl";

    var $pageUrl;
    var $object;        // can be name of db table
    var $opCode;
    var $defaultCode = "list";

    var $siteTitle = "";
    var $pageTitle = "";
    var $pageHeader = "";
    var $javaScripts = "";

    var $currentPage;
    var $rowsPerPage;
    var $rowsOptions = array (10, 20, 30, 50);

    var $orderBy;
    var $orderDir;
    var $orderDefault;

    var $data;
    var $errors = array ("err_count" => 0);

    var $lic_key = null;

    var $emailHeader = "";

    //--------------------------------------------------------------------------

    function XPage ($object = "none", $checkAccess = true)
    {
        @session_start ();

        global $db;
        $this->db = $db;

        if ( isset($_SESSION['LicenseAccess']) ) $this->LicenseAccess = $_SESSION['LicenseAccess'];

        $this->siteTitle = $this->db->GetSetting ("SiteTitle");
        $adminEmail = $this->db->GetSetting ("ContactEmail");
        $this->emailHeader = "From: {$this->siteTitle} <$adminEmail>\r\n";

        // check access rights
        if ($checkAccess) $this->CheckAccess ();


        $this->mainData = array ();
        $this->pageUrl = $_SERVER['PHP_SELF'];
        $this->object = $object;
        $this->RestoreState ();
        $this->opCode = $this->GetGP ("ocd", $this->defaultCode);
        $sql = $this->db->GetEntry("Select * From `currency` Where id='".$this->db->GetSetting("currency")."'", "");
        $this->currency_synbol=$sql['symbol'] ;
        $this->currency_name=$sql['name'] ;
        $ip_address = $this->GetServer ("REMOTE_ADDR", "unknown");

		if ($ip_address != "127.0.0.1")
		{

        		if ($this->object != "activation" And $this->CheckCurrentVersion () == 0)
        		{

            		if ($this->GetServerVersion () == 1) {
                		$this->Redirect ("./");
            		}
            		else {
                		$this->Redirect ("./activation.php");
            		}
        		}
        }
        
        $this->data = array ();
        
    }

    //--------------------------------------------------------------------------

    function HeaderCode ()
    {

        $cycling = $this->db->GetSetting ("cycling", 0);
        $currency = $this->currency_synbol;
        $hdrMenuLevels = ($cycling == 1)? "levels.php" : "levels_forced.php";
        $hdrMenuFee = ($cycling == 0)? "<li><a href='fees.php'>Fee</a></li>" : "";
        $hdrMenuLogout = "<a onClick=\"return confirm ('Do you really want to log out?');\" href='logout.php' class='menu' title='LogOut'>LogOut</a>";

        $datas = array (
            "HEADER_TITLE" => $this->siteTitle." : ".$this->pageTitle,
            "HEADER_JAVASCRIPTS" => $this->javaScripts,
            "HEADER_SERVER_TIME" => date ('M d Y H:i:s'),
            "SITE_TITLE" => $this->siteTitle,
            "MENU_LEVELS" => $hdrMenuLevels,
            "MENU_FEE" => $hdrMenuFee,
            "CURRENCY" => $currency,
            "CURRENT_YEAR" => date ('Y'),
            "MENU" => $this->getHtmlMenu()

        );

        return $datas;
    }

    function getHtmlMenu(){
        $access = (isset($_SESSION["A_access"])?(!empty($_SESSION["A_access"])?$_SESSION["A_access"]:array()):array());
        $cycling = $this->db->GetSetting ("cycling", 0);
        $hdrMenuLevels = ($cycling == 1)? "levels" : "levels_forced";

        $html = "";
        $file = str_replace('.php','',str_replace('/admin/','',$_SERVER['SCRIPT_NAME']));
        if ( in_array( 'stat', $access) ) $html.="<li class='active'><a href='stat.php'><span>Dashboard</span></a></li>";
        if ( in_array( 'members', $access) || in_array( 'tree', $access)) {
            if ( 'members' == $file || 'tree' == $file ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Members</span></a><ul style='display: $block;'>";
            if ( in_array( 'members', $access) ) $html.="<li><a href='members.php'><span>Members List</span></a></li>";
            if ( in_array( 'tree', $access) ) $html.="<li><a href='tree.php'><span>Overall Genealogy</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'inbox', $access) ) $html.="<li class='active'><a href='inbox.php'><span>Inbox</span></a></li>";
        if ( in_array( 'admindetails', $access) || in_array( 'settings', $access) || in_array( 'matrixes', $access) || in_array( 'levels', $access) || in_array( 'levels_forced', $access) || in_array( 'fees', $access) || in_array( 'admins', $access) || in_array( 'slider', $access) ) {
            if ( 'admindetails' == $file || 'settings' == $file ||  'matrixes' == $file || 'levels' == $file || 'levels_forced' == $file || 'admins' == $file || 'fees' == $file || 'slider' == $file) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Settings</span></a><ul style='display: $block;'>";
            if ( in_array( 'admindetails', $access) ) $html.="<li><a href='admindetails.php'><span>Admin Settings</span></a></li>";
            if ( in_array( 'settings', $access) ) $html.="<li><a href='settings.php'><span>Site Settings</span></a></li>";
            if ( in_array( 'matrixes', $access) ) $html.="<li><a href='matrixes.php'><span>Settings</span></a></li>";
            if ( in_array( $hdrMenuLevels, $access) ) $html.="<li><a href='{$hdrMenuLevels}.php'><span>Levels</span></a></li>";
            if ( in_array( 'fees', $access) && $cycling ==0 ) $html.="<li><a href='fees.php'><span>Fees</span></a></li>";
            if ( in_array( 'admins', $access) ) $html.="<li><a href='admins.php'><span>Administrators</span></a></li>";
            if ( in_array( 'slider', $access) ) $html.="<li><a href='slider.php'><span>Slider</span></a></li>";
            if ( in_array( 'pre_launch', $access) ) $html.="<li><a href='pre_launch.php'><span>Pre Launch</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'payment', $access) || in_array( 'cash', $access) || in_array( 'cash_out', $access) || in_array( 'processors', $access) || in_array( 'manual', $access) ) {
            if ( ( 'payment'== $file) || ( 'cash'== $file) || ( 'cash_out'== $file) || ( 'processors'== $file) || ( 'manual'== $file) ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Finance</span></a><ul style='display: $block;'>";
            if ( in_array( 'payment', $access) ) $html.="<li><a href='payment.php'><span>Payments</span></a></li>";
            if ( in_array( 'cash', $access) ) $html.="<li><a href='cash.php'><span>Cash History</span></a></li>";
            if ( in_array( 'cash_out', $access) ) $html.="<li><a href='cash_out.php'><span>Withdrawals</span></a></li>";
            if ( in_array( 'processors', $access) ) $html.="<li><a href='processors.php'><span>Processors</span></a></li>";
            if ( in_array( 'manual', $access) ) $html.="<li><a href='manual.php'><span>Manual Payment</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'pages', $access) || in_array( 'm_pages', $access) || in_array( 'news', $access) || in_array( 'faq', $access) || in_array( 'template_elements', $access) ) {
            if ( ( 'pages'== $file) || ( 'm_pages'== $file) || ( 'news'== $file) || ( 'faq'== $file) || ( 'template_elements'== $file) ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Site Content</span></a><ul style='display: $block;'>";
            if ( in_array( 'pages', $access) ) $html.="<li><a href='pages.php'><span>Public Pages</span></a></li>";
            if ( in_array( 'm_pages', $access) ) $html.="<li><a href='m_pages.php'><span>Backoffice Pages</span></a></li>";
            if ( in_array( 'news', $access) ) $html.="<li><a href='news.php'><span>News</span></a></li>";
            if ( in_array( 'faq', $access) ) $html.="<li><a href='faq.php'><span>FAQ</span></a></li>";
            if ( in_array( 'template_elements', $access) ) $html.="<li><a href='template_elements.php'><span>Template Elements</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'lands', $access) || in_array( 'aptools', $access) || in_array( 'ptools', $access) || in_array( 'tads', $access) ) {
            if ( ( 'lands'== $file) || ( 'aptools'== $file) || ( 'ptools'== $file) || ( 'tads'== $file) ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Promotions</span></a><ul style='display: $block;'>";
            if ( in_array( 'lands', $access) ) $html.="<li><a href='lands.php'><span>Landing Pages</span></a></li>";
            if ( in_array( 'aptools', $access) ) $html.="<li><a href='aptools.php'><span>Admin Banners</span></a></li>";
            if ( in_array( 'ptools', $access) ) $html.="<li><a href='ptools.php'><span>Members Banners</span></a></li>";
            if ( in_array( 'tads', $access) ) $html.="<li><a href='tads.php'><span>Text Ads</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'tickets', $access) || in_array( 'pub_tickets', $access)  ) {
            if ( ( 'tickets'== $file) || ( 'pub_tickets'== $file) ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Support</span></a><ul style='display: $block;'>";
            if ( in_array( 'tickets', $access) ) $html.="<li><a href='tickets.php'><span>For Members</span></a></li>";
            if ( in_array( 'pub_tickets', $access) ) $html.="<li><a href='pub_tickets.php'><span>For Visitors</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'categories', $access) || in_array( 'products', $access) || in_array( 'shopfee', $access) || in_array( 'shop_orders', $access) || in_array( 'shop_settings', $access)  ) {
            if ( ( 'categories'== $file) || ( 'products'== $file) || ( 'shopfee'== $file) || ( 'shop_orders'== $file) || ( 'shop_settings'== $file)  ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>E-Shop</span></a><ul style='display: $block;'>";
            if ( in_array( 'categories', $access) ) $html.="<li><a href='categories.php'><span>Categories</span></a></li>";
            if ( in_array( 'products', $access) ) $html.="<li><a href='products.php'><span>Products</span></a></li>";
            if ( in_array( 'shopfee', $access) ) $html.="<li><a href='shopfee.php'><span>Fees</span></a></li>";
            if ( in_array( 'shop_orders', $access) ) $html.="<li><a href='shop_orders.php'><span>Orders</span></a></li>";
            if ( in_array( 'shop_settings', $access) ) $html.="<li><a href='shop_settings.php'><span>Settings</span></a></li>";

            $html.="</ul></li>";
        }
        if ( in_array( 'templates', $access) || in_array( 'autorespondersf', $access) || in_array( 'autoresponders', $access) || in_array( 'atemplates', $access) || in_array( 'mailing', $access) ) {
            if ( ( 'templates'== $file) || ( 'autorespondersf'==$file) || ( 'autoresponders'==$file) || ( 'atemplates'== $file) || ( 'mailing'== $file) ) $block = 'block';else $block = 'none';
            $html.="<li class='has-sub'><a href='#'><span>Notifications</span></a><ul style='display: $block;'>";
            if ( in_array( 'templates', $access) ) $html.="<li><a href='templates.php'><span>System Notifications</span></a></li>";
            if ( in_array( 'autorespondersf', $access) ) $html.="<li><a href='autorespondersf.php'><span>Inactive Members Autoresponder</span></a></li>";
            if ( in_array( 'autoresponders', $access) ) $html.="<li><a href='autoresponders.php'><span>Active Members Autoresponder</span></a></li>";
            if ( in_array( 'atemplates', $access) ) $html.="<li><a href='atemplates.php'><span>Mass Mailing Templates</span></a></li>";
            if ( in_array( 'mailing', $access) ) $html.="<li><a href='mailing.php'><span>Send Mass Email</span></a></li>";
            $html.="</ul></li>";
        }
        if ( in_array( 'backup', $access) ) $html.="<li><a href='backup.php'><span>Backups</span></a></li>";

        return $html;        
    }

    //--------------------------------------------------------------------------
    // This function prepare array for main part of page
    // Should be re-defined
    function FooterCode ()
    {
        $datas = array (
            "FOOTER_COPYRIGHT" => "Copyright",
        );
        return $datas;
    }

    function checkLicense()
    {
        if (
            !isset($this->LicenseAccess[$this->object]) 
            || (isset($this->LicenseAccess[$this->object]) && $this->LicenseAccess[$this->object][$this->lic_key]==1 ) 
            || (isset($this->LicenseAccess[$this->object]) && isset($this->LicenseAccess[$this->object]['access']) && $this->LicenseAccess[$this->object]['access']==1 )  
        ) return true;
        else return false;
    }
    //--------------------------------------------------------------------------
    // Function call required method which should prepare array for main part of page
    function RunController ()
    {
        // execute required method or redirect to default view

        if ( !$this->checkLicense() ) 
        {
            $page = file_get_contents("http://runmlm.com/noLic.tpl");
            $this->mainTemplate = "./templates/noLic.tpl";
            $this->pageTitle = $this->LicenseAccess[$this->object]['title'];
            $this->pageHeader = $this->LicenseAccess[$this->object]['title'];
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "CONTENT" => $page
            );
        }
        else 
        {
            $method = "ocd_".$this->opCode;
            if (method_exists ($this, $method)) {
                $this->$method ();
            }
            else {
                $this->Redirect ($this->pageUrl);
            }
        }


    }

    //--------------------------------------------------------------------------
    // Render a page
    function Render ()
    {
        $this->RunController ();

        $tpl = new XTemplate ($this->mainTemplate);

        $tpl->assign_file ("HEADER_TEMPLATE", $this->headerTemplate);
        $tpl->assign_file ("FOOTER_TEMPLATE", $this->footerTemplate);

        $tpl->assign ($this->HeaderCode ());
        $tpl->parse ("MAIN.HEADER");

        $tpl->assign ($this->FooterCode ());
        $tpl->parse ("MAIN.FOOTER");

        $tpl->assign_array ("MAIN", $this->data);

        $tpl->out ("MAIN");

        $this->Close ();
    }

    //--------------------------------------------------------------------------
    // Close db connection and free memory etc.
    function Close ()
    {
        $this->db->Close ();
        $this->SaveState ();
    }


//== Authentication section ====================================================

    //--------------------------------------------------------------------------
    function RegisterUser ()
    {
        $login  = $this->GetGP ("Login");
        $passwd = md5 ($this->GetGP ("Password"));
        $real_login = $this->db->GetSetting ("AdminUsername");
        $real_passwd = $this->db->GetSetting ("AdminPassword");
//debug( serialize( array('login', 'admins', 'stat', 'members', 'tree', 'admindetails', 'settings', 'matrixes', 'levels', 'payment', 'cash', 'cash_out', 'processors', 'pages', 'm_pages', 'news', 'faq', 'lands', 'aptools', 'ptools', 'tads', 'tickets', 'pub_tickets', 'categories', 'products', 'templates', 'autorespondersf', 'autoresponders', 'atempplates', 'mailing', 'backups') ) );
       $sql_data = $this->db->GetEntry ("Select * From `user_admins` WHERE md5(username)='".md5($login)."' and md5(passwd)='".md5($passwd)."' ");

        $ip_check = $this->db->GetSetting ("SecurityMode", 0);

        //if ($login == $real_login and $passwd == $real_passwd)
        if (!empty($sql_data))
        {

            if ($ip_check == 0)
            {
                //$t = unserialize($sql_data['access']);
                //$t[]='prelaunch';
                //debug(serialize($t));//prelaunch
                $_SESSION['A_Login'] = $login;
                $_SESSION['A_Passwd'] = $passwd;
                $_SESSION['A_access'] = unserialize($sql_data['access']);
                $this->SetLog ("Logged in succesfully.");
                return 1;
            }
            else
            {
                $ip_visitor = $this->GetServer ("REMOTE_ADDR", "unknown");
                $ip_admin = $this->db->GetSetting ("IPAddress");
                if ($ip_visitor == $ip_admin)
                {
                    $_SESSION['A_Login'] = $login;
                    $_SESSION['A_Passwd'] = $passwd;
                    $_SESSION['A_access'] = unserialize($sql_data['access']);
                    $this->SetLog ("Logged in succesfully.");
                    return 1;
                }
                else
                {
                    $code = getUnID (8);
                    $siteUrl = $this->db->GetSetting("SiteUrl");
                    $contactEmail = $this->db->GetSetting ("ContactEmail");
                    $this->db->SetSetting ("pin_code", $code);
                    $m_subject = "Changing Security Data";
                    $message = "You have tried to enter from another IP address.\r\n";
                    $message .= "To change your current IP address click the link below end enter this pin-code: ".$code."\r\n";
                    $message .= $siteUrl."check_data_admin.php?c=".$code;
                    @mail ($contactEmail, $m_subject, $message, $this->emailHeader);
                    $this->SetLog ("Incorrect login attempt. Another IP.");
                    return -1;

                }
            }
        }
        else
        {
            $this->SetLog ("Incorrect login attempt. Password or Username do not match.");
            return -2;
        }

    }

    //--------------------------------------------------------------------------
    function SetLog ($text)
    {
        $ipaddr = $_SERVER["REMOTE_ADDR"];
        $this->db->ExecuteSql ("Insert Into `logs` (z_date, ip_addr, descr) Values ('".time()."', '$ipaddr', '$text')");
    }

    //--------------------------------------------------------------------------
    function CheckAccess ()
    {
        $login = $this->GetSession ("A_Login");
        $passwd = $this->GetSession ("A_Passwd");

        $sql_data = $this->db->GetEntry ("Select id, access From `user_admins` WHERE md5(username)='".md5($login)."' and md5(passwd)='".md5($passwd)."' " );

        $_SESSION["A_access"] = unserialize( $sql_data['access'] );

        $access = (isset($_SESSION["A_access"])?(!empty($_SESSION["A_access"])?$_SESSION["A_access"]:array()):array());

        $page = str_replace(array('admin/','/','.php'),'',$_SERVER['PHP_SELF']);
        
        if ( $page=='logout' ) return;

        if (empty($login) || empty($passwd)) {
            $this->Logout ();
        }

        //if ( empty($login) || empty($passwd) || !in_array( $page, $access) ) {
        if ( !in_array( $page, $access) ) {
            //$this->Logout ();
            //if ( $page!='blank' )
            if ( strpos($page,'blank')===false )
                $this->Redirect('blank.php');
        }

        $license_key =  $this->db->GetOne ("Select value From settings  Where keyname='LicenseNumber'");
//debug($license_key);
        $this->lic_key = base64_decode(substr($license_key,2));
        $HTTP_HOST = str_replace("www.",'',$_SERVER['HTTP_HOST']);
        if ( substr($HTTP_HOST,0,2)!=substr($license_key,0,2) || !in_array($this->lic_key, array('FREE', 'STARTER', 'STANDARD') ) )
            exit('Access denied #407');
//        if ( substr($_SERVER['HTTP_HOST'],0,2)!=substr($license_key,0,2) ||  !in_array($this->lic_key, array('FREE', 'STARTER', 'STANDARD') ) ) 

        //if ( empty( $this->lic_key)  || !is_array( $this->lic_key) || (is_array( $this->lic_key) && empty( $this->lic_key)) ) $this->lic_key=null;
        //$s = base64_encode('FREE'); debug( substr($_SERVER['HTTP_HOST'],0,2).$s );  //debug( $s[1].$s );

/*
        $real_login = $this->db->GetSetting ("AdminUsername");
        $real_passwd = $this->db->GetSetting ("AdminPassword");

        if ($login != $real_login or $passwd != $real_passwd) {
            $this->Logout ();
        }
*/
    }

    //--------------------------------------------------------------------------
    function UpdateRegisterDetails ()
    {
        $_SESSION['A_Login'] = $this->db->GetSetting ("AdminUsername");
        $_SESSION['A_Passwd'] = $this->db->GetSetting ("AdminPassword");;
    }

    //--------------------------------------------------------------------------
    function CheckCurrentVersion ()
    {
       $result = $this->db->ExecuteSql ("SHOW index FROM settings where column_name='keyname'");
        return $result->num_rows ;
        //$meta = mysql_fetch_field ($result);
        //return ($meta->multiple_key);
    }

    //--------------------------------------------------------------------------
    function SetCurrentVersion ($value = 0)
    {
        if ($value == 1 And $this->CheckCurrentVersion () == 0) {
            $this->db->ExecuteSql ("ALTER TABLE `settings` ADD INDEX `key1` (`keyname`)");
        }
        elseif ($value == 0 And $this->CheckCurrentVersion () == 1) {
            $this->db->ExecuteSql ("ALTER TABLE `settings` DROP INDEX `key1`");
            $this->db->ExecuteSql ("Update settings Set value='".UniKey()."' Where keyname='LicenseNumber'");
        }
    }

    //--------------------------------------------------------------------------
    function GetServerVersion ()
    {
        $domain_name = $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
        $domain_name = pathinfo ($domain_name);
        $domain_name = $domain_name["dirname"];
        $domain_name = substr ($domain_name, 0, -6);

        $secureCode = $this->db->GetOne ("Select value From settings Where keyname='SerialCode'");
        $PathSite = $this->db->GetOne ("Select value From `settings` Where keyname='PathSite'");

        $querry_com = "t=1&";
        $querry_com .= "dname=$domain_name&";
        $querry_com .= "code=$secureCode&";
        $querry_com .= "version=MLM Version3.7&";
        $querry_com .= "ocd=check37";
        $querry_ru = $querry_com;

        $reply_com = "";


        $ch_com = curl_init ();
        curl_setopt ($ch_com, CURLOPT_URL, ASERVER_COM);
        curl_setopt ($ch_com, CURLOPT_POST, true);
        curl_setopt ($ch_com, CURLOPT_POSTFIELDS, $querry_com);
        curl_setopt ($ch_com, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch_com, CURLOPT_TIMEOUT, 20);
        curl_setopt ($ch_com, CURLOPT_HEADER, false);
        $reply_com = curl_exec ($ch_com);
        curl_close ($ch_com);
/*
        $reply_ru = "";
        $ch_ru = curl_init ();
        curl_setopt ($ch_ru, CURLOPT_URL, ASERVER_RU);
        curl_setopt ($ch_ru, CURLOPT_POST, true);
        curl_setopt ($ch_ru, CURLOPT_POSTFIELDS, $querry_ru);
        curl_setopt ($ch_ru, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch_ru, CURLOPT_TIMEOUT, 10);
        curl_setopt ($ch_ru, CURLOPT_HEADER, false);
        $reply_ru = curl_exec ($ch_ru);
        curl_close ($ch_ru);
*/
        $reply_ru='';

//debug($reply_com);
        $reply_com = unserialize($reply_com)  ;
        if ( !is_array($reply_com) ) exit($reply_com);

        $access = base64_encode( serialize($reply_com['access']) );
        $access = $access[0].$access;
        $this->db->ExecuteSql ("Update settings Set value='".$access."' Where keyname='access'");

        $_SESSION['LicenseAccess'] = $reply_com['access'];
        $reply_com = $reply_com['status'];

        $filename = $PathSite."includes/config.php";
//debug($reply_com);
        if (($reply_com == "Another version" Or $reply_ru == "Another version") Or (($reply_com != "Activate" And $reply_com != "Access denied" And $reply_com != "Complete") And ($reply_ru != "Activate" And $reply_ru != "Access denied" And $reply_ru != "Complete")) Or ((strlen($reply_com) >= 17) Or (is_bool ($reply_com) And $reply_com) And (strlen($reply_ru) >= 17) Or (is_bool ($reply_ru) And $reply_ru)))
        {
            $contents = "";
            $handle = fopen ($filename, "r");
            $fstat = fstat ($handle);
            $contents = fread ($handle, filesize($filename));
            fclose ($handle);

            if ($reply_com == "Another version" Or $reply_ru == "Another version") {
                $this->db->ExecuteSql ("Update `settings` Set value='The current version of the product does not correspond with the version shown in the license. Your copy will remain active for three days.' Where keyname='AdminMessage'");
            }

            if (strrchr ($contents, chr(160)))
            {
                $handle = fopen ($filename, "w");
                $contents = substr ($contents, 0, -5);
                $contents = $contents."//?".">";
                fwrite ($handle, $contents);
                fclose ($handle);

                $this->SetCurrentVersion (1);
                return 1;
            }
            else
            {
                $new_time = time ();
                $old_time = $fstat['mtime'];

                if ($new_time - $old_time <= 60*60*24*3)
                {
                    $this->SetCurrentVersion (1);
                    return 1;
                }
                else {
                    $this->SetCurrentVersion (0);
                    $this->Redirect ("activation.php");
                }
            }
        }
        elseif ((!is_bool ($reply_com) And $reply_com == "Activate") Or (!is_bool ($reply_ru) And $reply_ru == "Activate"))
        {

            $contents = "";
            $handle = fopen ($filename, "r");
            $contents = fread ($handle, filesize($filename));
            fclose ($handle);

            $this->db->ExecuteSql ("Update `settings` Set value='' Where keyname='AdminMessage'");
            if (!strrchr ($contents, chr(160))) {

                $handle = fopen ($filename, "w");
                $contents = substr ($contents, 0, -5);
                $contents = $contents."//".chr(160)."?".">";
                fwrite ($handle, $contents);
                fclose ($handle);
            }
            $this->SetCurrentVersion (1);
            return 1;
        }
        else
        {
            $this->SetCurrentVersion (0);
            $this->Redirect ("activation.php");
        }
    }

    //--------------------------------------------------------------------------
    function Logout ()
    {
        $_SESSION = array ();
        session_destroy ();
        $this->Redirect ("./login.php");
    }


//== Paging and Sorting support section ========================================

    //--------------------------------------------------------------------------
    function Pages_GetFirstIndex ()
    {
        return $this->currentPage * $this->rowsPerPage;
    }

    //--------------------------------------------------------------------------
    function Pages_GetLastIndex ($total)
    {
        $toRet = $this->Pages_GetFirstIndex() + $this->rowsPerPage;
        if ($toRet > $total) $toRet = $total;
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function Pages_GetLimits ()
    {
        $start = $this->currentPage * $this->rowsPerPage;
        $toRet = " LIMIT $start, {$this->rowsPerPage} ";
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function Pages_GetLinks ($totalRows, $link)
    {
        $left = "";
        $right = "";

        $toRet = "<hr /><table style='width:100%;' cellspacing='0' cellpadding='5' border='0' align='left'><tr>";

        $toRet .= "<td valign='top' align='left' style='width:25%;'>
                <table cellspacing='0' cellpadding='5' border='0' align='left'>
                    <tr>
                        <td>Rows per page:</td>
                    </tr>
                </table>";
        foreach ($this->rowsOptions as $val) {
            $number = ($val == $this->rowsPerPage) ? "<b>{$val}</b>" : "<a href='{$link}rpp=$val&pg=0'>$val</a>";
            $toRet .= "<table cellspacing='0' cellpadding='5' border='0' align='left'>
                    <tr>
                        <td class='rows'>
                            $number
                        </td>
                    </tr>
                </table>";
        }
        $toRet .= "</td>";

        $toRet .= "<td style='width:10%;'>";
        $totalPages = ceil ($totalRows / $this->rowsPerPage);

        if ($totalPages > 1)
        {
            $toRet .= "<table cellspacing='0' cellpadding='5' border='0' align='right'>
                    <tr>
                        <td>Goto rows:</td>
                    </tr>
                </table>";
            $toRet .= "</td>";
            $toRet .= "<td valign='top' align='left'>";

            for ($i = 0; $i < $totalPages; $i++)
            {
                $start = $i * $this->rowsPerPage + 1;
                $end = $start + $this->rowsPerPage - 1;
                if ($end > $totalRows) $end = $totalRows;
                $pageNo = $left."$start-$end".$right;


                if ($i == $this->currentPage)
                    $pageNo = "<b class='pages'>$pageNo</b>";
                else
                    $pageNo = "<a href='".$link."pg=$i' class='pages'>$pageNo</a>";



                $pageNo = "<table cellspacing='0' cellpadding='5' border='0' style='float: left;'>
                    <tr>
                        <td>
                            $pageNo
                        </td>
                    </tr>
                </table> ";
                $toRet .= $pageNo;
            }
        }
        $toRet .= "</td>";
        return $toRet."</tr></table>";
    }
    //--------------------------------------------------------------------------
    function Header_GetSortLink ($field, $title = "")
    {
        if ($title == "") $title = $field;
        $drctn = ($this->orderDir == "asc") ? "desc" : "asc";
        $toRet = "<a href='{$this->pageUrl}?order=$field&drctn=$drctn'>$title</a>";

        if ($field == $this->orderBy) {
            $toRet .= "<img src='./images/sort_{$this->orderDir}.gif' width='10' border='0'>";
        }

        return $toRet;
    }

    //--------------------------------------------------------------------------
    function Header_GetSortLinkS ($field, $title = "")
    {
        if ($title == "") $title = $field;
        $drctn = ($this->orderDir == "asc") ? "desc" : "asc";
        $toRet = "<a href='{$this->pageUrl}?ocd=sellist&order=$field&drctn=$drctn'><b>$title</b></a>";

        if ($field == $this->orderBy) {
            $toRet .= "<img src='./images/sort_{$this->orderDir}.gif' width='10' border='0'>";
        }

        return $toRet;
    }


//== Validation support section ================================================

    //--------------------------------------------------------------------------
    function GetValidGP ($key, $name, $type = VALIDATE_NOT_EMPTY, $defValue = "")
    {
        $value = $defValue;
        if (array_key_exists ($key, $_GET)) $value = trim ($_GET [$key], "\x00..\x20");
        elseif (array_key_exists ($key, $_POST)) $value = trim ($_POST [$key], "\x00..\x20");

        switch ($type)
        {
            case VALIDATE_NOT_EMPTY:
                if ($value == "") {
                    $this->SetError ($key, "You should specify '$name'.");
                }
                break;

            case VALIDATE_USERNAME:
                if (preg_match ("/^[\w]{4,50}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' has to consist of from 4 up to 12 symbols.");
                }
                break;

            case VALIDATE_PASSWORD:
                //if (preg_match("/^[\w]{5,12}\$/i", $value) == 0) {
                if (preg_match("/^[0-9a-zA-Z!@#$%^&*]{5,12}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' has consist of from 4 up to 50 symbols.");
                }
                break;

            case VALIDATE_PASS_CONFIRM:
                if ($value != $name) {
                    $this->SetError ($key, "Passwords don't match.");
                }
                break;

            case VALIDATE_EMAIL:
                if (preg_match ("/^[-_\.0-9a-z]+@[-_\.0-9a-z]+\.+[a-z]{2,4}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' is wrong.");
                }
                break;

            case VALIDATE_INT_POSITIVE:
                if (!is_numeric ($value) or (preg_match ("/^\d+\$/i", $value) == 0)) {
                    $this->SetError ($key, "Field '$name' has to be integer positive number.");
                }
                break;

            case VALIDATE_FLOAT_POSITIVE:
                if (!is_numeric ($value) or (preg_match ("/^[\d]+\.+[\d]+\$/i", $value) == 0)) {
                    $this->SetError ($key, "Field '$name' should be a positive float (format: 12.34).");
                }
                break;

            case VALIDATE_CHECKBOX:
                if ($value == $defValue) {
                    $this->SetError ($key, "You have to take '$name'.");
                }
                break;

            case VALIDATE_NUMERIC:
                if (!is_numeric ($value)) {
                    $this->SetError ($key, "'$name' should be a numeric.");
                }
                break;

            case VALIDATE_NUMERIC_POSITIVE:
                if (!is_numeric ($value) Or $value < 0) {
                    $this->SetError ($key, "'$name' should be a numeric.");
                }
                break;

        }

        return $value;
    }

    //--------------------------------------------------------------------------
    function SetError ($key, $text)
    {
        $this->errors['err_count']++;
        $this->errors[$key] = $text;
    }

    //--------------------------------------------------------------------------
    function GetError ($key)
    {
        return (array_key_exists ($key, $this->errors)) ? $this->errors[$key] : "";
    }



//== Common functions section ==================================================

    //--------------------------------------------------------------------------
    function Redirect ($targetURL)
    {
        $this->Close ();
        header ("Location: $targetURL");
        exit ();
    }

    //--------------------------------------------------------------------------
    function enc ($value)
    {
        $search = array ("/</", "/>/", "/'/");
        $replace = array ("&lt;", "&gt;", "&#039;");
        return preg_replace ($search, $replace, $value);
    }

    //--------------------------------------------------------------------------
    function dec ($value)
    {
        $search = array ("/&lt;/", "/&gt;/", "/&#039;/");
        $replace = array ("<", ">", "'");
        return preg_replace ($search, $replace, $value);
    }

    //--------------------------------------------------------------------------
    function GetGPC ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_GET)) $toRet = trim ($_GET [$key]);
        elseif (array_key_exists ($key, $_POST)) $toRet = trim ($_POST [$key]);
        elseif (array_key_exists ($key, $_COOKIE)) $toRet = trim ($_COOKIE [$key]);

        return (get_magic_quotes_gpc ()) ? stripslashes ($toRet) : $toRet;
    }

    //--------------------------------------------------------------------------
    function GetID($key)
    {
        $toRet = 0;
        if (array_key_exists($key, $_GET)) {
            $toRet = trim($_GET [$key]);
        } elseif (array_key_exists($key, $_POST)) {
            $toRet = trim($_POST [$key]);
        }
        if (!is_numeric($toRet)) $toRet = 0;
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function GetGP ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_GET)) $toRet = (is_array($_GET [$key])) ? $_GET [$key] : trim ($_GET [$key]);
        elseif (array_key_exists ($key, $_POST)) $toRet = (is_array($_POST [$key])) ? $_POST [$key] : trim ($_POST [$key]);
        return (/*get_magic_quotes_gpc ()*/false) ? stripslashes ($toRet) : $toRet;
    }

    //--------------------------------------------------------------------------
    function GetGPArray ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_GET)) $toRet = $_GET [$key];
        elseif (array_key_exists ($key, $_POST)) $toRet = $_POST [$key];
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function GetSession ($str, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($str, $_SESSION)) $toRet = trim ($_SESSION [$str]);
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function GetServer ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_SERVER)) $toRet = trim ($_SERVER [$key]);
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function GetStateValue ($key2, $defValue = "")
    {
        $toRet = $defValue;
        $key1 = "a_".$this->object;
        if (array_key_exists ($key1, $_SESSION)) {
            if (array_key_exists ($key2, $_SESSION[$key1])) {
                $toRet = $_SESSION [$key1][$key2];
            }
        }
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function SaveStateValue ($key2, $value)
    {
        $key1 = "a_".$this->object;
        $_SESSION[$key1][$key2] = $value;
    }

    //--------------------------------------------------------------------------
    function RemoveStateValue ($key2)
    {
        $key1 = "a_".$this->object;
        unset ($_SESSION[$key1][$key2]);
    }

    //--------------------------------------------------------------------------
    function SaveState ()
    {
        $key = "a_".$this->object;
        $_SESSION[$key]['pg'] = $this->currentPage;
        $_SESSION[$key]['rpp'] = $this->rowsPerPage;
        $_SESSION[$key]['order'] = $this->orderBy;
        $_SESSION[$key]['drctn'] = $this->orderDir;
    }

    //--------------------------------------------------------------------------
    function RestoreState ()
    {
        // Get current page index
        $this->currentPage = ($this->GetGP ("pg") != "") ? $this->GetGP ("pg") : $this->GetStateValue ("pg", 0);
        $this->rowsPerPage = ($this->GetGP ("rpp") != "") ? $this->GetGP ("rpp") : $this->GetStateValue ("rpp", 20);
        $this->orderBy = ($this->GetGP ("order") != "") ? $this->GetGP ("order") : $this->GetStateValue ("order", $this->orderDefault);
        $this->orderDir = ($this->GetGP ("drctn") != "") ? $this->GetGP ("drctn") : $this->GetStateValue ("drctn", "asc");

        $this->SaveState ();
    }

    //--------------------------------------------------------------------------
    function getUserAgent ($user_agent)
    {
        $browser = "unknown";
        if (eregi ("Opera", $user_agent)) $broser = "Opera";
        if (eregi ("MSIE", $user_agent)) $browser = "MS Internet Explorer";
        if (eregi ("Netscape", $user_agent)) $browser = "Netscape";
        if (eregi ("Mozilla", $user_agent) and !eregi ("MSIE", $user_agent)) $browser = "Mozilla";

        return $browser;
    } 

   function FCKeditor($name,$text='',$ToolbarSet='wind',$height='500') {
        include_once $_SERVER['DOCUMENT_ROOT']."/admin/editor/fckeditor.php";
        $ed='tmp_'.time();
        $$ed = new FCKeditor($name) ;
        $$ed->Config['DefaultLanguage'] = 'en' ;
        $$ed->BasePath = '/admin/editor/';
        //$$ed->EditorAreaCSS = MAIN_URL.'templates/default/css/style.css';
        $$ed->ToolbarSet = $ToolbarSet ; // Default  Basic1  Short  short1  wind  wind1
        $$ed->DefaultLanguage='en';
        $$ed->Value = $text;
        $$ed->Width  = 900 ;
        $$ed->Height = $height ;
        return $$ed->CreateHtml();
    }


}


//------------------------------------------------------------------------------
// XDB - MySQL Database class
class XDB
{
    var $dbConnect;

    //--------------------------------------------------------------------------
    function XDB ()
    {
        // open DB connection
        $this->dbConnect = $this->OpenDbConnect ();

        $this->ExecuteSql ("Set character_set_client='utf8'");
        $this->ExecuteSql ("Set character_set_results='utf8'");
        $this->ExecuteSql ("Set collation_connection='utf8_unicode_ci'");
    }
    

    //--------------------------------------------------------------------------
    function OpenDbConnect ($host = DbHost, $dbName = DbName, $login = DbUserName, $pwd = DbUserPwd)
    {
        $connect = mysqli_connect($host, $login, $pwd) or die('Error connect database. '.mysqli_error($connect));
        mysqli_select_db($connect,$dbName) or die('ERROR: '.mysqli_error($connect));
        return $connect;

        // $connect = mysql_connect ($host, $login, $pwd);
        // mysql_select_db ($dbName);
        // return $connect;
    }

    //--------------------------------------------------------------------------
    function ExecuteSql ($sql, $withPaging = false)
    {
        global $zPage;
        if ($withPaging) {
            return mysqli_query ($this->dbConnect,$sql.$zPage->Pages_GetLimits());
        }
        else {
            return mysqli_query ($this->dbConnect,$sql)  ;
        }
    }


    //--------------------------------------------------------------------------
    function GetOne ($sql, $defVal = "")
    {
        $toRet = $defVal;
        $result = $this->ExecuteSql ($sql);
        if ($result != false) {
            $line = mysqli_fetch_row ($result);
            $toRet = $line[0];
            $this->FreeSqlResult ($result);
        }
        if ($toRet == NULL) $toRet = $defVal;
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function GetEntry ($sql, $redir_url = "")
    {
//debug($sql,false);
//debug(debug_backtrace(),false);
        $result = $this->ExecuteSql ($sql);
        if ($row = $this->FetchInArray ($result))
        {
            $this->FreeSqlResult ($result);
            return $row;
        }
        else
        {
            if (strlen ($redir_url) > 0) {
                $this->Close ();
                header ("Location: $redir_url");
                exit ();
            }
            else {
                return false;
            }
        }
    }

    //--------------------------------------------------------------------------
    function FetchInArray ($result)
    {
        return mysqli_fetch_array ($result);
    }

    //--------------------------------------------------------------------------
    function FetchInAssoc ($result)
    {
        return mysqli_fetch_assoc($result);
    }
    
    //--------------------------------------------------------------------------
    function FreeSqlResult ($result)
    {
        mysqli_free_result ($result);
    }

    //--------------------------------------------------------------------------
    function GetInsertID ()
    {
        return mysqli_insert_id ($this->dbConnect);
    }

    //--------------------------------------------------------------------------
    function GetSetting ($keyname, $defVal = "")
    {
        $toRet = $defVal;
        $result = $this->ExecuteSql ("Select value From settings Where keyname='$keyname'");
        if ($result != false) {
            $line = mysqli_fetch_row ($result);
            $toRet = $line[0];
            mysqli_free_result ($result);
        }
        if ($toRet == NULL) $toRet = $defVal;
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function SetSetting ($keyname, $value)
    {
        $this->ExecuteSql ("Update `settings` Set value='$value' Where keyname='$keyname'");
    }

    //--------------------------------------------------------------------------
    function Close ()
    {
        mysqli_close ($this->dbConnect);
    }

    //--------------------------------------------------------------------------
    function NumRows ($result)
    {
        return mysqli_num_rows($result);
    }

    //--------------------------------------------------------------------------
    function Real ($str='')
    {
        return mysqli_real_escape_string($this->dbConnect, $str);
    }

    //--------------------------------------------------------------------------
    function FetchInAssos ($result)
    {
        return mysqli_fetch_assoc($result);
    }


}

?>

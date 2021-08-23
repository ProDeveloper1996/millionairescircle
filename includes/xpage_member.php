<?php

@ini_set ("max_execution_time", 60);
@set_time_limit (60);
@ini_set ("display_errors", "0");
@error_reporting (E_ALL);
//@set_magic_quotes_runtime (0);

define ("VALIDATE_NOT_EMPTY", 1);
define ("VALIDATE_USERNAME", 2);
define ("VALIDATE_PASSWORD", 3);
define ("VALIDATE_PASS_CONFIRM", 4);
define ("VALIDATE_EMAIL", 5);
define ("VALIDATE_INT_POSITIVE", 6);
define ("VALIDATE_INT_ZIP", 11);
define ("VALIDATE_FLOAT_POSITIVE", 7);
define ("VALIDATE_CHECKBOX", 8);
define ("VALIDATE_NUMERIC", 9);
define ("VALIDATE_NUMERIC_POSITIVE", 10);
define ("VALIDATE_REPLICA", 12);
define ("VALIDATE_URL", 13);
define ("SYSTEM", 0);
define ("BAR_HEIGHT", 120);

define ("FORM_EMPTY", 1);
define ("FORM_FROM_DB", 2);
define ("FORM_FROM_GP", 3);

define ("SiteStatusWaiting", 0);
define ("SiteStatusEnable", 1);
define ("SiteStatusPending", 2);
define ("SiteStatusSuspended", 3);
$siteStatusArray = array (SiteStatusWaiting => "Waiting", SiteStatusEnable => "Enable", SiteStatusPending => "Pending", SiteStatusSuspended => "Suspended");
$typeAdvertArray = array (0 => "Graphical Banner", 1 => "Textual Banner");
$typeAmountArray = array (0 => "Credits", 1 => "Cash");
$typeViewArray = array (0 => "Once per member", 1 => "Once per day");
$cashoutStatusArray = array (0 => "Pending", 1 => "Complete", 2 => "Denied");

$cfg_color["width"] = 10;
$cfg_color["height"] = 10;
$cfg_color["cols"] = 6;
$cfg_color["rows"] = 2;
$colorArray = array (1 => "0000ff", 2 => "00ff00", 3 => "ff0000",
                     4 => "00ffff", 5 => "ff00ff", 6 => "ffff00");

$db = new XDB ();

require_once ($db->GetSetting("PathSite")."lang/".LANG."/system.php");
require_once ($db->GetSetting("PathSite")."lang/".LANG."/member.php");

class XPage
{
    var $db = null;

    var $mainTemplate;
    var $headerTemplate = "./templates/header.tpl";
    var $footerTemplate = "./templates/footer.tpl";

    var $pageUrl;
    var $object;    // name of db table
    var $opCode;
    var $defaultCode = "list";

    var $pageTitle = "";
    var $pageHeader = "";
    var $javaScripts = "";

    var $currentPage;
    var $rowsPerPage;
    var $rowsOptions = array (10, 20, 30, 50);

    var $orderBy;
    var $orderDir;
    var $orderDefault;
    var $orderDirDefault;

    var $data;
    var $errors = array ("err_count" => 0);

    var $member_id;
    var $emailHeader = "";
    var $upgradeTitle = "";

    var $LicenseAccess = array();
    var $lic_key = null;

    //--------------------------------------------------------------------------

    function XPage ($object = "none", $checkAccess = true)
    {
        @session_start ();

        global $db;
        $this->db = $db;

        $t = unserialize(base64_decode(substr($this->db->GetSetting ("access"),1)) );
        if ( !empty($t)  && is_array($t) ) $_SESSION['LicenseAccess'] = $t;
        if ( isset($_SESSION['LicenseAccess']) ) $this->LicenseAccess = $_SESSION['LicenseAccess'];
        $license_key =  $this->db->GetOne ("Select value From settings  Where keyname='LicenseNumber'");
        $this->lic_key = base64_decode(substr($license_key,2));
        $HTTP_HOST = str_replace("www.",'',$_SERVER['HTTP_HOST']);
        if ( substr($HTTP_HOST,0,2)!=substr($license_key,0,2) || !in_array($this->lic_key, array('FREE', 'STARTER', 'STANDARD') ) )
            exit('Access denied');
//        if ( substr($_SERVER['HTTP_HOST'],0,2)!=substr($license_key,0,2) || !in_array($this->lic_key, array('FREE', 'STARTER', 'STANDARD') ) ) 

        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $adminEmail = $this->db->GetSetting ("ContactEmail");
        $this->emailHeader = "From: $siteTitle <$adminEmail>\r\n";

        checkPreLaunchEnd();
        
        if ( isPreLaunch() )
            $this->headerTemplate = "./templates/header_PRE_LAUNCH.tpl";
        
        // check access rights
        if ($checkAccess) $this->CheckAccess ();

        $this->mainData = array ();
        $this->pageUrl = $_SERVER['PHP_SELF'];
        $this->object = $object;
        $this->RestoreState ();
        $this->opCode = $this->GetGP ("ocd", $this->defaultCode);
        $this->data = array ();
        $this->member_id = $this->GetSession ("MemberID");
        $this->UserOnline ();

        $sql = $this->db->GetEntry("Select * From `currency` Where id='".$this->db->GetSetting("currency")."'", "");
        $this->currency_synbol=$sql['symbol'] ;
        $this->currency_name=$sql['name'] ;

        if ( isset($_POST['checkPay']) ){
            //require_once("bitaps/config.php");
            require_once("blockchain/config.php");
            $class = new Bitcoin();
            exit( $class->checkPaid($_POST['address'], 0) );
        }


    }

    //--------------------------------------------------------------------------

    function HeaderCode ()
    {
        global $db,$dict;
        $siteTitle = $this->db->GetSetting ("SiteTitle");
        $pay_mode = $this->db->GetSetting ("PaymentMode");
        $is_pif = $this->db->GetSetting ("is_pif");
        $member_id = $this->member_id;
        $status = getStatus ($member_id);
        $currency = $this->currency_synbol;
        $cycling = $this->db->GetSetting ("cycling", 0);
        
        $useBanners = $this->db->GetSetting ("useBanners", "");

        $avatar = '';
        $physical_path = $this->db->GetSetting ("PathSite");
        $filename = "avatar_".$member_id.'.jpg';
        if ( file_exists ($physical_path."data/avatar/".$filename) ) $avatar = '<img src="/data/avatar/'.$filename.'" alt="prof" />';

        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'", "");
        $m_level = $this->db->GetOne ("Select `m_level` From `members` Where member_id='$member_id'", 1);
        $level_title = ($m_level == 0)? "Unpaid Level" : $this->dec ($this->db->GetOne ("Select title From `types` Where order_index='$m_level'"));

	$overview_class = (basename ($this->pageUrl) == "overview.php")? "active" : "";
        $hdrMenuOverview = "<li class='".$overview_class."'><a href='overview.php'>Overview</a></li>";

		$account_class = (basename ($this->pageUrl) == "account.php")? "active" : "";
        $hdrMenuAccount = "<li class='".$account_class."'><a href='account.php' >Account Details</a></li>";

		$myaccount_class = (basename ($this->pageUrl) == "myaccount.php")? "active" : "";
        $hdrMyMenuAccount = "<li class='".$myaccount_class."'><a href='myaccount.php' >{$dict['Menu_MyAccount']}</a></li>";

		if ($cycling == 1)
        {
            $matrix_class = (basename ($this->pageUrl) == "matrix.php")? "active" : "";
            $hdrMenuMatrix = "<li class='".$matrix_class."'><a href='tree_matrix1.php' >{$dict['Menu_MyMatrices']}</a></li>";
            
            $payment_class = (basename ($this->pageUrl) == "payment.php")? "active2" : "inactive2";
            $hdrMenuPayment = ($m_level == 0)? "<li><a href='payment.php' ><i class='fa fa-arrow-circle-o-right fa-lg'></i>{$dict['Menu_PaymentPage']}</a></li>" : ""; 
            
        }
        else
        {
            $levels_class = (basename ($this->pageUrl) == "tree_matrix.php" Or basename ($this->pageUrl) == "matrix.php")? "active" : "";
            $hdrMenuMatrix = "<li class='".$levels_class."'><a href='tree_matrix.php' >{$dict['Menu_MyMatrix']}</a></li>";

            if (isPreLaunch()) $hdrMenuMatrix = '';

            $payment_class = (basename ($this->pageUrl) == "payment_f.php")? "active2" : "inactive2";
            $hdrMenuPayment = "<li><a href='payment_f.php' ><i class='fa fa-arrow-circle-o-right fa-lg'></i>{$dict['Menu_PaymentPage']}</a></li>";
            
        }

        //if ($this->LicenseAccess['tree_matrix'][$this->lic_key]==0) $hdrMenuMatrix = '';

		$secure_class = (basename ($this->pageUrl) == "security.php")? "active" : "inactive";
        $hdrMenuSecure = "<td class='center_".$secure_class."'><a href='security.php' class='menu'>{$dict['Menu_SecurityPage']}</a></td>";

        $ticket_class = (basename ($this->pageUrl) == "tickets.php")? "active" : "";
        $hdrMenuSupport = "<li class='".$ticket_class."'><a href='tickets.php'>{$dict['Menu_Support']}</a></li>";
        if (isset($this->LicenseAccess['tickets']) && $this->LicenseAccess['tickets'][$this->lic_key]==0) $hdrMenuSupport = '';

        $ptools_class = (basename ($this->pageUrl) == "ptools.php")? "active2" : "inactive2";
        $hdrMenuPtools = ($m_level>0?"<li><a href='ptools.php' ><i class='fa fa-picture-o fa-lg'></i>{$dict['Menu_MyBanners']}</a></li>":'');
        
        $friend_class = (basename ($this->pageUrl) == "tellfriends.php")? "active2" : "inactive2";
        $hdrMenuFriend = "<li><a href='tellfriends.php' ><i class='fa fa-comments fa-lg'></i>{$dict['Menu_TellFriends']}</a></li>"; 
        
        
        $payments_class = (basename ($this->pageUrl) == "payments.php")? "active2" : "inactive2";
        $hdrMenuPayments = "<li><a href='payments.php' ><i class='fa fa-history fa-lg'></i>{$dict['Menu_PaymentHistory']}</a></li>";

        $cash_class = (basename ($this->pageUrl) == "cash.php")? "active2" : "inactive2";
        $hdrMenuCash = "<li><a href='cash.php' ><i class='fa fa-money fa-lg'></i>{$dict['Menu_CashHistory']}</a></li>";
        
        $pif_class = (basename ($this->pageUrl) == "pif.php")? "active2" : "inactive2";
        $hdrMenuPif = "<li><a href='pif.php' class='$pif_class'><i class='fa fa-arrow-circle-o-right fa-lg'></i>{$dict['Menu_PayItForward']}</a></li>";

        $with_class = (basename ($this->pageUrl) == "withdrawal.php")? "active2" : "inactive2";
        $hdrMenuCashOuts = "<li><a href='withdrawal.php' ><i class='fa fa-credit-card fa-lg'></i>{$dict['Menu_Withdrawals']}</a></li>"; 

        $hdrRestMenu ="";
        $count_pages = $this->db->GetOne ("Select Count(*) From `pages` Where is_active=1 And is_member=1 And level".$m_level."=1", 0);
        if ($count_pages > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `pages` Where is_active=1  And is_member=1 And level".$m_level."=1 Order By order_index Asc");
            $p_id = $this->GetID ("p_id");
            while ($row = $this->db->FetchInArray ($result))
            {
                $page_id = $row['page_id'];
                $page_name = $this->dec ($row['menu_title']);
                $p_class = ($page_id == $p_id)? "active2" : "inactive2";
                
                //$hdrRestMenu .= "<tr><td class='w_padding' align='left'><a href='m_content.php?p_id=$page_id' class='white'>$page_name</a></td></tr>";
                $hdrRestMenu .= "<li><a href='m_content.php?p_id=$page_id' class='white'>$page_name</a></li>";
                
            }

        }
        //if ($hdrRestMenu == "") $hdrRestMenu = "No info...";
        
        
        $total_news = $this->db->GetOne ("Select Count(*) From `news` Where is_active='1' And destination=1", 0);
        $read_all_news = ($total_news > 0)? "<a href='news.php' class='tooSmallLink'>Read all news</span>" : "";
        
        $is_replica_m = $this->db->GetOne ("Select is_replica From `members` Where member_id='$member_id'", 0);
        //$replica = $this->db->GetOne ("Select replica From `members` Where member_id='$member_id'", 0);
        $is_replica_a = $this->db->GetSetting ("is_replica", 0);
        
        $replica_class = (basename ($this->pageUrl) == "replica_site.php")? "active2" : "inactive2";
        //And $replica != ""
		  $replicated_site = ($is_replica_a == 1 )? "<li><a href='replica_site.php' ><i class='fa fa-globe fa-lg'></i>{$dict['Menu_MySite']}</a></li>" : "";
        
        $aptools = $this->db->GetOne ("Select Count(*) From `aptools` Where `is_active`='1'", 0);
        $aptools_class = (basename ($this->pageUrl) == "aptools.php")? "active2" : "inactive2";
        $hdrAptools = ($aptools > 0 And $m_level > 0)? "<li><a href='aptools.php' ><i class='fa fa-picture-o fa-lg'></i>{$dict['Menu_SiteBanners']}</a></li>" : "";
        
        $tads_class = (basename ($this->pageUrl) == "tads.php")? "active2" : "inactive2";
        $hdrMenuTads = ($status == "active")? "<li><a href='tads.php' ><i class='fa fa-file-text fa-lg'></i>{$dict['Menu_MyTextAds']}</a></li>" : "";
        $quant_textadds = $this->db->GetSetting ("quant_textadds");
        if ( $quant_textadds ==0) $hdrMenuTads='';

        $ip_address = $this->GetServer ("REMOTE_ADDR", "unknown"); 
         
        
        $quant_textadds_show_m = $this->db->GetSetting ("quant_textadds_show_m");
        $text_ads = "";
        $tads_total = $this->db->GetOne ("Select COUNT(*) From `text_ads`", 0);		
        		$result = $this->db->ExecuteSql ("Select * From `text_ads` Order By Rand() Limit $quant_textadds_show_m");
        		while ($row = $this->db->FetchInArray ($result))
        		{
            		$id =$this->dec ($row['text_ad_id']);
            		$content = getTextAdContentShow ($id);
            		$text_ads .= "<td valign='top' style='background-color: #f6f6f6;padding:2px;border:1px solid #cccccc;line-height:15px;'>".$content."</td><td style='width:20px;'>&nbsp;<td>";
            		$this->db->ExecuteSql ("Update `text_ads` Set `displayed`=`displayed` + 1 Where `text_ad_id`='$id'");
        		}
        		$this->db->FreeSqlResult ($result);

        $cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='$member_id'", "0.00");
        $cash = sprintf ("%01.2f", $cash);

        $processor = $this->db->GetOne ("Select processor From `members` Where member_id='{$this->member_id}'", 0);
        $account_id = $this->db->GetOne ("Select account_id From `members` Where member_id='{$this->member_id}'", "");
        $min_cash_out = $this->db->GetSetting ("MinCashOut", 0);
        //if ($cash >= $min_cash_out And $processor > 0 And $account_id != "")
            $WITHDRAW_MONEY = "<a href='/member/cash_out.php'>{$dict['Cash_MakeWithdrawalRequest']}</a>";

        $ticket_class = (basename ($this->pageUrl) == "inbox.php")? "active" : "";
        $badge = getInboxBadge($member_id);
        $MENU_INBOX = "<li class='".$ticket_class."'><a href='inbox.php'>Inbox $badge</a></li>";

        $datas = array (
            "AMOUNT_EARNED_SUMM" => $cash,
            "WITHDRAW_MONEY" => $WITHDRAW_MONEY,
            "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
            "HEADER_JAVASCRIPTS" => $this->javaScripts,
            "HEADER_SERVER_TIME" => date ('M d Y H:i:s'),
            "HEADER_MEMBER_ID" => $this->member_id,
            "MEMBER_HEADER" => "Account #$member_id: $name / $level_title",
            
            "MEMBER_AVATAR"=> $avatar,
            "MEMBER_DATA" => $name,
            "CURRENCY" => $currency,
            "MENU_OVERVIEW" => $hdrMenuOverview,
            "MENU_ACCOUNT" => $hdrMenuAccount,

            "MENU_MYACCOUNT" => $hdrMyMenuAccount,

            "MENU_SECURE" => $hdrMenuSecure,
            "MENU_SUPPORT" => $hdrMenuSupport,
            "MENU_MATRIX" => $hdrMenuMatrix,

			      "MENU_TADS" => $hdrMenuTads,

            "MENU_PAYMENT" => $hdrMenuPayment,
            
            "MENU_PAYMENTS" => $hdrMenuPayments,
            "MENU_CASH" => $hdrMenuCash,
            "MENU_PIF" => ($status == "active" And $is_pif == 1)? $hdrMenuPif : "",
            
            "MENU_CASH_OUTS" => $hdrMenuCashOuts,
            "REST_MENU" => $hdrRestMenu,
            "REPLICATED_SITE" => $replicated_site,
            "MENU_FRIEND" => $hdrMenuFriend,
            "SITE_TITLE" => $siteTitle,
            "CURRENT_YEAR" => date ('Y'),
            
            "MENU_PTOOLS" => ($useBanners == "1")? $hdrMenuPtools : "",
            
            "READ_ALL_NEWS" => $read_all_news,
            "MENU_APTOOLS" => $hdrAptools,
            "TEXT_ADS" => $text_ads,

            'MENU_INBOX' => $MENU_INBOX,

        );

        if (isPreLaunch())
        {
            $datas["AMOUNT_STRUC"] = 'ДОДЕЛАТЬ';//find_number_members_level($member_id);
            $datas["AMOUNT_REF"] = $this->db->GetOne ("Select Count(*) From `members` Where enroller_id='$member_id' And is_dead=0", 0);

            $datas["START_TIME_BLOCK"][] = array (
                "START_TIME" => GetTimePreLaunch()
            );
        }
            
        
        if ($hdrRestMenu != "") $datas["REST_MENU_SHOW"] = array ("_" => "_");

        if ($quant_textadds_show_m > 0 And $tads_total > 0)
        {
				$datas["TADS"] = array ("_" => "_");
        }

        $total = $this->db->GetOne ("Select Count(*) From `news` Where is_active=1 And destination=1", 0);
        if ($total > 0)
        {
            $news = '';//array ();
            $result = $this->db->ExecuteSql ("Select * From `news` Where is_active=1 And destination=1 Order By news_date Desc Limit 3");
            while ($row = $this->db->FetchInArray ($result))
            {
                // $article = nl2br ($row['article']);
                // $date = date ("d.m.Y", $row['news_date']);
                // $news[] = array (
                //     "ROW_ID" => $row['news_id'],
                //     "ROW_DATE" => $date,
                //     "ROW_ARTICLE" => $article,
                // );
                $title = nl2br ($row['title']);
                $date = date ("d.m.Y", $row['news_date']);
                    $news.='
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <span class="news-date">'.$date.'</span>
                            <div class="colaps-nav collapsed" data-toggle="collapse" href="#news-item-'.$row['news_id'].'" aria-expanded="false">'.$title.'</div>
                            <!-- Item collapse -->
                            <div class="news-collapse collapse" id="news-item-'.$row['news_id'].'">
                                '.nl2br($this->dec($row['description'])).'
                            </div>
                            <!-- Item collapse End-->
                        </div>
                    ';
            }
            $this->db->FreeSqlResult ($result);
            // $datas["NEWS"][] = array (
            //     "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
            //     "NEWS_ROW" => $news,
            // );
            $datas['NEWS']= $news;
        }
        else
        {
            $datas["NEWS"][] = array (
                "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
                "N_EMPTY" => array ("HEADER_TITLE" => $siteTitle." : ".$this->pageTitle)
            );
        }
        
        $useEshop = $this->db->GetSetting ("useEshop", 0);
        if ($useEshop == 1)
        {
            $total = $this->db->GetOne ("Select Count(*) From `categories` Where `is_active`=1", 0);
            if ($total > 0)
            {
                $categories = array ();
                $result = $this->db->ExecuteSql ("Select * From `categories` Where is_active=1 Order By `title` Asc");
                while ($row = $this->db->FetchInArray ($result))
                {
                    $category_id = $row['category_id'];
                    $title = $this->dec ($row['title']);
                    $m_level_cat = $row['m_level'];
                    
                    $arrLevels = explode (";", $m_level_cat);
                    
                    $link = (in_array ($m_level, $arrLevels))? "<a href='./eshop.php?cid=$category_id' class='tooSmallLink'><i class='fa fa-arrow-circle-right fa-lg'></i>$title</a>" : "<span class='inactiveShop' title='You cannot see this category.'>".$title."</span>";
                    
                    $categories[] = array (
                        "ROW_LINK" => $link,
                    );
                }
                $this->db->FreeSqlResult ($result);
                $datas["SHOP"][] = array (
                    "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
                    "SHOP_ROW" => $categories,
                );
            }
            else
            {
                $datas["SHOP"][] = array (
                    "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
                    "S_EMPTY" => array ("HEADER_TITLE" => $siteTitle." : ".$this->pageTitle)
                );
            }    
        }

         $WAIT1_HTML = '';
         $data_member = $this->db->GetEntry ("Select * From `members` Where `member_id`=$member_id",false); 
         if ($data_member['first_name']=='' ||  $data_member['last_name']=='' )
            $WAIT1_HTML =  'Please <a href="./myaccount.php?accesssettings">specify your name</a>.<br>';
         if ($data_member['processor']==0 && !isPreLaunch() )
            $WAIT1_HTML .=  'Please fulfill the field with your <a href="./myaccount.php?paymentsettings">Processor data</a> to be able to withdraw funds.<br>';

        
         if ($WAIT1_HTML!='' )
            $datas["WAIT1"][] = array (
                "WAIT1_HTML" => $WAIT1_HTML,
            );

        return $datas;
    }

    //--------------------------------------------------------------------------
    // This function prepare array for main part of page
    // Should be re-defined
    function FooterCode ()
    {
        GLOBAL $dict;
		  $siteTitle = $this->db->GetSetting ("SiteTitle");
        $copyright = $this->dec ($this->db->GetOne ("Select value From settings Where keyname='Copyright'"));

        $datas = array (
            "FOOTER_COPYRIGHT" => $copyright,
            "FOOTER_CONTENT" => $this->db->GetSetting ("FooterContent")
            
        );

        $total_news = $this->db->GetOne ("Select Count(*) From `news` Where is_active='1' And destination=1", 0);
        $read_all_news = ($total_news > 0)? "<tr><td style='text-align:center;' colspan=2><a href='news.php' class='white'>{$dict['News_AllNews']}</span></td></tr>" : "";
        $datas+= array (
            "READ_ALL_NEWS" => $read_all_news,
		  );
        $news = '';
        if ($total_news > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `news` Where is_active='1' And destination=1 Order By news_date Desc Limit 3");
            while ($row = $this->db->FetchInArray ($result))
            {
                $title = nl2br ($row['title']);
                $date = date ("d.m.Y", $row['news_date']);
                $news.= "
						<tr>
							<td width='50'>".($row['photo']!=''?"<img src='/data/news/small_{$row['photo']}.jpg'>":'')."</td>
							<td>
								<span class='question'>$date</span><br />
								<a class='white' href='news_details.php?nid={$row['news_id']}'>$title</a>
							</td>
						</tr>
						<tr style='height:5px;'><td></td></tr>
						<tr style='height:1px;'><td colspan=2 class='mnu_divider'></td></tr>
						<tr style='height:10px;'><td></td></tr>
					 ";
            }
            $this->db->FreeSqlResult ($result);
            $datas['NEWS_LIST']= $news;
            $datas['NEWS']=array ("_" => "_");
        }
        else
        {
            $datas['NEWS_EMPTY']="
						<tr>
							<td align='center'>
								{$dict['News_NoNews']}
							</td>
						</tr>
				";
        }

        $useEshop = $this->db->GetSetting ("useEshop", 0);
        if ($useEshop == 1)
        {
            $total = $this->db->GetOne ("Select Count(*) From `categories` Where `is_active`=1", 0);
            if ($total > 0)
            {
                $categories = array ();
                $result = $this->db->ExecuteSql ("Select * From `categories` Where is_active=1 Order By `title` Asc");
                while ($row = $this->db->FetchInArray ($result))
                {
                    $category_id = $row['category_id'];
                    $title = $this->dec ($row['title']);
                    $m_level_cat = $row['m_level'];
                    
                    $arrLevels = explode (";", $m_level_cat);

				        $m_level = $this->db->GetOne ("Select `m_level` From `members` Where member_id='{$this->member_id}'", 1);

                    $link = (in_array ($m_level, $arrLevels))? "<a href='./eshop.php?cid=$category_id' class='white' >$title</a>" : "<span class='white' title='You cannot see this category.'>".$title."</span>";
                    
                    $categories[] = array (
                        "ROW_LINK" => $link,
                    );
                }
                $this->db->FreeSqlResult ($result);
                $datas["SHOP"][] = array (
                    "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
                    "SHOP_ROW" => $categories,
                );
            }
            else
            {
                $datas["SHOP"][] = array (
                    "HEADER_TITLE" => $siteTitle." : ".$this->pageTitle,
                    "S_EMPTY" => array ("HEADER_TITLE" => $siteTitle." : ".$this->pageTitle)
                );
            }    
        }

        return $datas;
    }

    //--------------------------------------------------------------------------
    // Function call required method which should prepare array for main part of page
    function RunController ()
    {
        // execute required method or redirect to default view
        $method = "ocd_".$this->opCode;
        if (method_exists ($this, $method)) {
            $this->$method ();
        }
        else {
            $this->Redirect ($this->pageUrl);
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

        $tpl->assign_array ("MAIN.HEADER", $this->HeaderCode ());

        //$tpl->assign ($this->FooterCode ());
        //$tpl->parse ("MAIN.FOOTER");

        $tpl->assign_array ("MAIN.FOOTER", $this->FooterCode ());


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
        $login  = $this->GetGP ("login");
        $passwd = $this->GetGP ("password");
        $passwd = md5 ($passwd);
        $member_id = $this->db->GetOne ("Select member_id From members Where username='$login' And passwd='$passwd' And is_active=1", 0);

        if ($member_id > 0)
        {
            $_SESSION['Username'] = $login;
            $_SESSION['MemberID'] = $member_id;
            $ip_visitor = $this->GetServer ("REMOTE_ADDR", "unknown");
            return true;   // Access granted
        }
        else
        {
            return false;  // Active member not found
        }
    }

    //--------------------------------------------------------------------------
    function UserOnline ()
    {
        $sessID = session_id ();

        $this->db->ExecuteSql ("Update online_stats Set member_id='{$this->member_id}', z_date='".time()."' Where session_id='$sessID'");
        if ($this->db->GetOne ("Select count(*) from online_stats where session_id='$sessID'") == 0) {
            $this->db->ExecuteSql ("Insert Into online_stats (session_id, member_id, z_date) Values ('$sessID', '{$this->member_id}', '".time()."')");
        }
    }

    //--------------------------------------------------------------------------
    function GetUserOnline ()
    {
        $countMembers = $this->db->GetOne ("Select Count(*) From online_stats Where member_id!=0 And z_date>".(time() - 5*60), 0);
        $countGuests = $this->db->GetOne ("Select Count(*) From online_stats Where member_id=0 And z_date>".(time() - 5*60), 0);

        return "$countMembers member(s) <br>$countGuests guest(s)";
    }

    //--------------------------------------------------------------------------
    function CheckAccess ()
    {
        $id = $this->GetSession ("MemberID");
        $count = $this->db->GetOne ("Select Count(*) From members Where member_id='$id'", 0);

        if ($count == 0) {
            $this->Logout ();
        }
    }

    //--------------------------------------------------------------------------
    function UpdateRegisterDetails ()
    {
        $id = $this->GetSession ("MemberID");
        $_SESSION['Login'] = $this->db->GetOne ("Select username From `members` Where member_id='$id'");
    }

    //--------------------------------------------------------------------------
    function CheckCurrentVersion ()
    {
        $result = $this->db->ExecuteSql ("Select keyname From settings Limit 1");
        $meta = mysql_fetch_field ($result);
        return ($meta->multiple_key);
    }

    //--------------------------------------------------------------------------
    function Logout ()
    {
        $_SESSION = array ();
        session_destroy ();
        $this->Redirect ("../index.php");
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
        GLOBAL $dict;
		  $left = "";
        $right = "";

        $toRet = "
            <label class='col-sm-2 col-md-2 control-label' style='margin-top: 7px;'>{$dict['Rowsperpage']}</label>
             <div class='col-sm-3'>
                        <ul class='pagination'>
        ";
        foreach ($this->rowsOptions as $val) {
            $toRet .= "<li  ".($val == $this->rowsPerPage?"class='active'":'')."><a href='{$link}rpp=$val&pg=0'>$val</a></li>";
        }
        $toRet .= "</ul></div>";
        
        $toRet .= "<div class='col-sm-7'>";
        $totalPages = ceil ($totalRows / $this->rowsPerPage);
        
        if ($totalPages > 1)
        {
            $toRet .= "<label class='col-sm-3 col-md-3 control-label'>{$dict['Goto']}</label>";
            $toRet .= "<div class='col-sm-9'>
                        <ul class='pagination'>
            ";

            for ($i = 0; $i < $totalPages; $i++)
            {
                $start = $i * $this->rowsPerPage + 1;
                $end = $start + $this->rowsPerPage - 1;
                if ($end > $totalRows) $end = $totalRows;
                $pageNo = $left."$start-$end".$right;

                // if ($i == $this->currentPage)
                //     $pageNo = "<b class='pages'>$pageNo</b>";
                // else
                //     $pageNo = "<a href='".$link."pg=$i' class='pages'>$pageNo</a>";
                // $toRet .= $pageNo;
                $toRet .= "<li  ".($i == $this->currentPage?"class='active'":'')."><a href='".$link."pg=$i' class='pages'>$pageNo</a></li>";

            }
            $toRet .= "</ul></div>";
        }
        $toRet .= "</div>";
        return $toRet;
    }

    //--------------------------------------------------------------------------
    function Header_GetSortLink ($field, $title = "", $module = "")
    {
        if ($module == "")
        {
            if ($title == "") $title = $field;
            $drctn = ($this->orderDir == "asc") ? "desc" : "asc";
            $toRet = "<a class='pages' href='{$this->pageUrl}?order=$field&drctn=$drctn'><b>$title</b></a>";

            if ($field == $this->orderBy)
            {
                $toRet .= "<img src='./images/sort_{$this->orderDir}.gif' width='10' border='0'>";
            }
        }
        else
        {
            if ($title == "") $title = $field;
            $drctn = ($this->orderDir == "asc") ? "desc" : "asc";
            $toRet = "<a class='pages' href='{$this->pageUrl}?mod=$module&order=$field&drctn=$drctn'><b>$title</b></a>";

            if ($field == $this->orderBy)
            {
                $toRet .= "<img src='./images/sort_{$this->orderDir}.gif' width='10' border='0'>";
            }
        }
        return $toRet;
    }

//== Validation support section ================================================

    //--------------------------------------------------------------------------
    function GetValidGP ($key, $name, $type = VALIDATE_NOT_EMPTY, $defValue = "")
    {
        GLOBAL $dict;
		  $value = $defValue;
        if (array_key_exists ($key, $_GET)) $value = trim ($_GET [$key], "\x00..\x20");
        elseif (array_key_exists ($key, $_POST)) $value = trim ($_POST [$key], "\x00..\x20");

        switch ($type)
        {
            case VALIDATE_NOT_EMPTY:
                if ($value == "") {
                    $this->SetError ($key, "{$dict['VALIDATE_NOT_EMPTY']} '$name'.");
                }
                break;

            case VALIDATE_USERNAME:
                if (preg_match ("/^[\w]{4,12}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_USERNAME']}");
                }
                break;

            case VALIDATE_PASSWORD:
                //if (preg_match("/^[\w]{5,12}\$/i", $value) == 0) {
                if (preg_match("/^[0-9a-zA-Z!@#$%^&*]{5,12}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_PASSWORD']}");
                }
                break;

            case VALIDATE_PASS_CONFIRM:
                if ($value != $name) {
                    $this->SetError ($key, "{$dict['VALIDATE_PASS_CONFIRM']}");
                }
                break;

            case VALIDATE_EMAIL:
                if (preg_match ("/^[-_\.0-9a-z]+@[-_\.0-9a-z]+\.+[a-z]{2,5}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_EMAIL']}");
                }
                break;

            case VALIDATE_INT_POSITIVE:
                if (!is_numeric ($value) or (preg_match ("/^\d+\$/i", $value) == 0)) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_INT_POSITIVE']}");
                }
                break;

            case VALIDATE_INT_ZIP:
                if (!is_numeric ($value) or (preg_match ("/^\d+\$/i", $value) == 0) or $value <= 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_INT_ZIP']}");
                }
                break;

            case VALIDATE_FLOAT_POSITIVE:
                if (!is_numeric ($value) or (preg_match ("/^[\d]+\.+[\d]+\$/i", $value) == 0)) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_FLOAT_POSITIVE']}");
                }
                break;

            case VALIDATE_CHECKBOX:
                if ($value == $defValue) {
                    $this->SetError ($key, "{$dict['VALIDATE_CHECKBOX']} '$name'.");
                }
                break;

            case VALIDATE_NUMERIC_POSITIVE:
                if (!is_numeric ($value) Or $value <= 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_NUMERIC_POSITIVE']}");
                }
                break;
            
            case VALIDATE_REPLICA:
                if (preg_match ("/^[a-zA-Z]{2,15}\$/i", $value) == 0) {
                    $this->SetError ($key, "'$name' {$dict['VALIDATE_REPLICA']}");
                }
                break;
                
			case VALIDATE_URL:
                if ($value == "" Or $value == "http://") {
                    $this->SetError ($key, "{$dict['VALIDATE_URL']} '$name'.");
                }
                break;
        }

        return $value;
    }
    
    //--------------------------------------------------------------------------
    function validEmail ($value)
    {
        if (preg_match ("/^[-_\.0-9a-z]+@[-_\.0-9a-z]+\.+[a-z]{2,4}\$/i", $value) == 0)
        {
            return false;            
        }
        else
        {
            return true;
        }
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
        $search = array ("/&/", "/</", "/>/", "/'/");
        $replace = array ("&amp;", "&lt;", "&gt;", "&#039;");
        return preg_replace ($search, $replace, $value);
    }

    //--------------------------------------------------------------------------
    function dec ($value)
    {
        $search = array ("/&amp;/", "/&lt;/", "/&gt;/", "/&#039;/");
        $replace = array ("&", "<", ">", "'");
        return preg_replace ($search, $replace, $value);
    }


    //--------------------------------------------------------------------------
    function GetGPC ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_GET)) $toRet = trim ($_GET [$key]);
        elseif (array_key_exists ($key, $_POST)) $toRet = trim ($_POST [$key]);
        elseif (array_key_exists ($key, $_COOKIE)) $toRet = trim ($_COOKIE [$key]);

        return /*(get_magic_quotes_gpc ()) ? stripslashes ($toRet) :*/ $toRet;
    }

    //--------------------------------------------------------------------------
    function GetGP ($key, $defValue = "")
    {
        $toRet = $defValue;
        if (array_key_exists ($key, $_GET)) $toRet = trim ($_GET [$key]);
        elseif (array_key_exists ($key, $_POST)) $toRet = trim ($_POST [$key]);
        
        return /*(get_magic_quotes_gpc ()) ? stripslashes ($toRet) : */$toRet;
    }

    //--------------------------------------------------------------------------
    function GetID ($key)
    {
        $toRet = 0;
        if (array_key_exists ($key, $_GET)) {
            $toRet = trim ($_GET [$key]);
        }
        elseif (array_key_exists ($key, $_POST)) {
            $toRet = trim ($_POST [$key]);
        }
        if (!is_numeric ($toRet)) $toRet = 0;
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
        $key1 = "m_".$this->object;
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
        $key1 = "m_".$this->object;
        $_SESSION[$key1][$key2] = $value;
    }

    //--------------------------------------------------------------------------
    function RemoveStateValue ($key2)
    {
        $key1 = "m_".$this->object;
        unset ($_SESSION[$key1][$key2]);
    }

    //--------------------------------------------------------------------------
    function SaveState ()
    {
        $key = "m_".$this->object;
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
        $this->orderDir = ($this->GetGP ("drctn") != "") ? $this->GetGP ("drctn") : $this->GetStateValue ("drctn", $this->orderDirDefault);

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

    //--------------------------------------------------------------------------

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
            return mysqli_query ($this->dbConnect,$sql);
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

}

?>
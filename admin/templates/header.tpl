<!-- BEGIN: HEADER -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
    <title>{HEADER_TITLE}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="content-language" content="en">
    <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>

    <script type="text/javascript" src="./js/vtip-min.js"></script>
    <script src="./js/menu.js" type="text/javascript"></script>
    <script src="./js/spoiler.js" type="text/javascript"></script>
    <script language="JavaScript" type="text/javascript" src="../js/clock.js"></script>
    <link rel="shortcut icon" href="/{SITE_FOLDER}images/favicon.ico" type="image/x-icon" />
    <link href="./css/styles.css" type="text/css" rel="stylesheet" />
    <link href="./css/menu.css" type="text/css" rel="stylesheet" />
    <link href="./css/table.css" type="text/css" rel="stylesheet" />
    <link href="./css/vtip.css" type="text/css" rel="stylesheet" />
    {HEADER_JAVASCRIPTS}
    <script language='JavaScript'>
    <!--
        var theTime = new Date ("{HEADER_SERVER_TIME}");
        var month = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    //-->
    </script>


</head>

<body onLoad="clock ();">

<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
<tr>
    <td valign='top'>

    <table width='100%' border='0' cellspacing='0' cellpadding='0' >
        <tr>
             <td width='50' style="padding:7px;">
                 <img src='./images/slogo.png' />
             </td>
             <td>
                 <h1>{SITE_TITLE} Administrative panel</h1>
             </td>
            <td align="right"><img src='./images/clock.png' /> <span id="disp1"></span></td>
        </tr>
    </table>

    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
            <td width='10%' style='vertical-align:top'>
<div style='floa'>
	<div id='cssmenu'>
		<ul>
        {MENU}
<!--
		   <li class='active'><a href='stat.php'><span>Dashboard</span></a></li>

				 <li class='has-sub'><a href='#'><span>Members</span></a>
					<ul>
					   <li><a href='members.php'><span>Members List</span></a></li>
					   <li><a href='tree.php'><span>Overall Genealogy</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Settings</span></a>
					<ul>
					   <li><a href='admindetails.php'><span>Admin Settings</span></a></li>
					   <li><a href='settings.php'><span>Site Settings</span></a></li>
             <li><a href='matrixes.php'><span>Settings</span></a></li>
             <li><a href='{MENU_LEVELS}'><span>Levels</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Finance</span></a>
					<ul>
					   <li><a href='payment.php'><span>Payments</span></a></li>
             <li><a href='cash.php'><span>Cash History</span></a></li>
             <li><a href='cash_out.php'><span>Withdrawals</span></a></li>
             <li><a href='processors.php'><span>Processors</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Site Content</span></a>
					<ul>
					   <li><a href='pages.php'><span>Public Pages</span></a></li>
             <li><a href='m_pages.php'><span>Backoffice Pages</span></a></li>
             <li><a href='news.php'><span>News</span></a></li>
             <li><a href='faq.php'><span>FAQ</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Promotions</span></a>
					<ul>
					   <li><a href='lands.php'><span>Landing Pages</span></a></li>
             <li><a href='aptools.php'><span>Admin Banners</span></a></li>
             <li><a href='ptools.php'><span>Members Banners</span></a></li>
             <li><a href='tads.php'><span>Text Ads</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Support</span></a>
					<ul>
					   <li><a href='tickets.php'><span>For Members</span></a></li>
             <li><a href='pub_tickets.php'><span>For Visitors</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>E-Shop</span></a>
					<ul>
					   <li><a href='categories.php'><span>Categories</span></a></li>
             <li><a href='products.php'><span>!Products</span></a></li>
             <li><a href='fees.php'><span>Fees</span></a></li>
					</ul>
				 </li>
         <li class='has-sub'><a href='#'><span>Notifications</span></a>
					<ul>
					   <li><a href='templates.php'><span>System Notifications</span></a></li>
             <li><a href='autorespondersf.php'><span>Inactive Members Autoresponder</span></a></li>
             <li><a href='autoresponders.php'><span>Active Members Autoresponder</span></a></li>
             <li><a href='atemplates.php'><span>Mass Mailing Templates</span></a></li>
             <li><a href='mailing.php'><span>Send Mass Email</span></a></li>
					</ul>
				 </li>
		   <li><a href='backup.php'><span>Backups</span></a></li>
-->
		   <li><a onClick="return confirm ('Do you really want to log out?');" href="logout.php"><span>Logout</span></a></li>
		</ul>
	</div>
</td>

<td valign='top' width='80%' style='padding:0 5px 0 5px;'>




<!-- END: HEADER -->
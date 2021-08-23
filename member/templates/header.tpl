<!-- BEGIN: HEADER -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{HEADER_TITLE}</title>
	<meta name="robots" content="index,follow" />
	<meta name="keywords" content="{HEADER_KEYWORDS}" />
	<meta name="description" content="{HEADER_DESCRIPTION}" />
	<meta name="revisit-after" content="3 days" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-extend.css" rel="stylesheet">
      
    <!-- owl carouse 2-->
    <link href="/css/owl.carousel.css" rel="stylesheet">
    <link href="/css/owl.theme.default.min.css" rel="stylesheet">

    <!-- Font Awesome styles -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
      
    <!-- Custom styles -->
    <link href="/css/custom.css" rel="stylesheet">

    <script src="/js/jquery_1.11.2.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {HEADER_JAVASCRIPTS}
  </head>

  <body>

    <!-- Static navbar -->
    <nav class="navbar navbar-static-top navbar-fixed-top navbar-custom">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-logo" href="./myaccount.php">{SITE_TITLE}</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav nav-main">
          {MENU_MYACCOUNT}
          {MENU_MATRIX}
          {MENU_SHOP}
          {MENU_SUPPORT}

          </ul>

          <ul class="nav navbar-nav btn-login">
            <li class="login-prof">{MEMBER_AVATAR} {DICT.Top_Welcome} {MEMBER_DATA}</li>
            <li class="login-n"><a href="/member/logout.php">{DICT.Top_Logout}</a></li>  
          </ul>

        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="cntainer-fluid">
        <div class="container">
            <div class="row">
                <div class="row-same-height">
                    <div class="col-xs-12 col-sm-4 col-md-3 col-sm-height nav-left">
                        
                        <h3>{DICT.Left_AMOUNT}</h3>
                        <div >
                          <a href="/member/cash.php" class="amount_earned">{CURRENCY}{AMOUNT_EARNED_SUMM}</a><br>
                        <ul>
                         <li>{WITHDRAW_MONEY}</li>
                        </ul>
                        </div>
                        
                        <h3>{DICT.Left_FINANCES}</h3>
                        <ul>
                         {MENU_PAYMENT}
                        {MENU_PIF}
                        {MENU_PAYMENTS}
                        {MENU_CASH}
                        {MENU_CASH_OUTS}
                        </ul>
                        
                        <h3>{DICT.Left_PROMOTION}</h3>
                        <ul>
                        {MENU_TADS}
                        {MENU_FRIEND}
                        {MENU_PTOOLS}
                        {MENU_APTOOLS}
                        {REPLICATED_SITE}
                        </ul>
                        
            <!-- BEGIN: REST_MENU_SHOW -->
                        <hr />
                        <h3>INFO</h3>
                        <ul>
                          {REST_MENU}
                        </ul>
            <!-- END: REST_MENU_SHOW -->



            <!-- BEGIN: SHOP -->
                        <hr />
                        <h3>E-SHOP CATEGORIES</h3>
                        <ul>
                    <!-- BEGIN: SHOP_ROW -->
                            <li>{ROW_LINK}</li>
                    <!-- END: SHOP_ROW -->
                           <!-- BEGIN: S_EMPTY -->
                                    <li>No products...</li>
                           <!-- END: S_EMPTY -->
                            <li><a href="./download.php"><i class="fa fa-arrow-circle-down fa-lg"></i>Download Page</a></li>
                        </ul>
            <!-- END: SHOP -->

                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-9 col-sm-height content-right">

<!-- BEGIN: WAIT1-->
  <div class="alert alert-warning"><span class="fa fa-warning " style="width: 30px;color: #E80800;font-size: 18px;"></span>{WAIT1_HTML}</div>
<!-- END: WAIT1 -->

<!-- END: HEADER -->
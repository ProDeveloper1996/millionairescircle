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

    <link href="/css/C3counter.css" rel="stylesheet">

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
<nav class="navbar navbar-static-top navbar-fixed-top">
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

                    <!-- BEGIN: START_TIME_BLOCK-->
                    <div class="special">
                        <div id="counter">
                            <div id="shading">&nbsp;</div>
                        </div>
                        <div class="name">
                            <span>day</span>
                            <span>hour</span>
                            <span>min</span>
                            <span>sec</span>
                        </div>
                        <script type="text/javascript" src="/js/C3counter.js"></script>
                        <script type="text/javascript">
                            // Default options
                            C3Counter("counter", { startTime :{START_TIME} });
                        </script>
                    </div>
                    <!-- END: START_TIME_BLOCK -->

                    <h3 style="text-align: center;">Amount of members in your structure</h3>
                    <ul>
                        <li style="color: #004fab;font-weight: bold;text-align: center;">{AMOUNT_STRUC}</li>
                    </ul>

                    <h3 style="text-align: center;">Amount of direct referrals</h3>
                    <ul>
                        <li style="color: #004fab;font-weight: bold;text-align: center;">{AMOUNT_REF}</li>
                    </ul>

                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-height content-right">

                    <!-- BEGIN: WAIT1-->
                    <div class="alert alert-warning"><span class="fa fa-warning " style="width: 30px;color: #E80800;font-size: 18px;"></span>{WAIT1_HTML}</div>
                    <!-- END: WAIT1 -->

                    <!-- END: HEADER -->
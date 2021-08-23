<!-- BEGIN: HEADER -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{HEADER_TITLE}</title>
    <meta name="robots" content="index,follow"/>
    <meta name="keywords" content="{HEADER_KEYWORDS}"/>
    <meta name="description" content="{HEADER_DESCRIPTION}"/>
    <meta name="revisit-after" content="3 days"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="en"/>

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
<nav class="navbar navbar-static-top navbar-fixed-top navbar-custom">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-logo" href="/">{SITE_TITLE}</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav nav-main">
                <li class="{ACTIVE_HOME}"><a href='/index.php'>{DICT.Home}</a></li>
                {MENU_NEWS}
                {REST_MENU} 
                {MENU_FAQ}
                {MENU_SUPPORT}
            </ul> 

            <!-- BEGIN: START_TIME_BLOCK-->
            <div class="special" style="float: right;margin-top: 10px;color: #fff;">
                <div id="counter">
                    <div id="shading_">&nbsp;</div>
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

   
        </div><!--/.nav-collapse -->
    </div>
</nav>
<!-- END: HEADER -->
<!-- BEGIN: MAIN -->
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
            <ul class="nav navbar-nav nav-main navbar-customtext">
                <li class="active"><a href='/index.php'>{DICT.Home}</a></li>
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
<br style="height:2px;">

<div class="container-fluid carousel_main">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4 tab-content">
                <!-- form-login -->
                <div class="tab-pane form-login {TAB_ACTIVE_LOGIN}" role="tabpanel" id="form-login">
                    <div class="form-login-content">
                        <p><a class="link-type-1" href="#form-registration" aria-controls="settings" role="tab"
                              data-toggle="tab">{DICT.Left_SignUp}</a> &nbsp; or &nbsp; <strong
                                    class="strong-type-1"> {DICT.Left_Login}</strong></p>
                        <p>&nbsp;</p>
                        <form name='LoginForm' action='{MAIN_ACTION}' method='POST'>

                            <div class="form-group">
                                <span class='error'>{LOGIN_ERROR}</span>
                            </div>
                            <div class="form-group">
                                <input name='Username' type="text" class="form-control"
                                       placeholder="{DICT.Left_Username}">
                            </div>
                            <div class="form-group">
                                <input name='Password' type="password" class="form-control"
                                       placeholder="{DICT.Left_Password}">
                            </div>
                            <!-- BEGIN: TURING_ROW -->
                            <div class="form-group">
                                <input class='form-control' type='text' name='turing' value='' maxlength='5'
                                       style='width: 100px;  display: initial;'
                                       autocomplete='off'>&nbsp;{LOGIN_TURING_IMAGE}
                            </div>
                            <!-- END: TURING_ROW -->

                            <p><strong class="strong-type-1">* </strong><a
                                        href='forgot_password.php'>{DICT.Left_ForgotPassword}</a></p>
                            <button type="submit" class="btn btn-form-login">{DICT.Left_LogInBut}</button>
                            <input type='hidden' name='ocd' value='login'/>
                        </form>
                    </div>
                </div>
                <!-- form-login end -->

                <!-- form-registration -->
                <div class="tab-pane form-login {TAB_ACTIVE_REG}" role="tabpanel" id="form-registration">
                    <div class="form-login-content">
                        <p><strong class="strong-type-1">{DICT.Left_SignUp}</strong> &nbsp; or &nbsp; <a
                                    class="link-type-1" href="#form-login" aria-controls="settings" role="tab"
                                    data-toggle="tab"> {DICT.Left_Login}</a></p>
                        <p>{MAIN_ENROLLER} <a href="" data-toggle="tooltip" data-placement="top"
                                              title="{MAIN_ENROLLER_B}">[?]</a></p>
                        <form name='SignUpForm' action='{MAIN_ACTION}' method='POST'>
                            <!--
                              <div class="form-group">
                                <span class='error'>{MAIN_FIRSTNAME_ERROR}</span>
                                <input type="text" name='first_name' class="form-control" placeholder="{DICT.Left_FirstName}" value="{MAIN_FIRSTNAME}">
                              </div>  
                              <div class="form-group">
                                <span class='error'>{MAIN_LASTNAME_ERROR}</span>
                                <input type="text" name='last_name' class="form-control" placeholder="{DICT.Left_LastName}" value="{MAIN_LASTNAME}">
                              </div>
-->
                            <div class="form-group">
                                <span class='error'>{MAIN_EMAIL_ERROR}</span>
                                <input type="text" name='email' class="form-control"
                                       placeholder="{DICT.Left_EmailAddress}" value="{MAIN_EMAIL}">
                            </div>
                            <div class="form-group">
                                <span class='error'>{MAIN_USERNAME_ERROR}</span>
                                <input type="text" name='username' class="form-control"
                                       placeholder="{DICT.Left_ChooseUsername}" value="{MAIN_USERNAME}">
                            </div>
                            <!--
                              <div class="form-group">
                                <span class='error'>{MAIN_PASSWORD_ERROR}</span>
                                <input type="password" name='password' class="form-control" placeholder="{DICT.Left_ChoosePassword}" value="{MAIN_PASSWORD}">
                              </div>
                              <div class="form-group">
                                <span class='error'>{MAIN_PASSWORD2_ERROR}</span>
                                <input type="password" name='password2' class="form-control" placeholder="{DICT.Left_RePassword}" value="{MAIN_PASSWORD2}">
                              </div>
-->
                            <!-- BEGIN: TURING -->
                            <div class="form-group">
                                <span class='error'>{MAIN_TURING_ERROR}</span>
                                <input class='form-control' type='text' name='turing' value='{MAIN_TURING}'
                                       maxlength='12' style='width: 100px;  display: initial;' autocomplete='off'>
                                &nbsp; {MAIN_TURING_IMAGE}<br>
                                <span class='question'>{DICT.Left_TuringText}</span>
                            </div>
                            <!-- END: TURING -->

                            <div class="checkbox">
                                <span class='error'>{MAIN_AGREE_ERROR}</span>
                                <label>
                                    <input type="checkbox" name='agree' value='1' {MAIN_AGREE}> {DICT.Left_Agree1} <a
                                            href="content.php?p_id=2">{DICT.Left_Terms}</a>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-form-login">{DICT.Left_RegisterMe}</button>
                            <input type='hidden' name='ocd' value='register'/>
                        </form>
                    </div>
                </div>
                <!-- form-registration end -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="owl-carousel owl-main hidden-xs">
            {SLIDER}
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!-- END: HEADER -->

<div class="container-fluid welcome-block">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                {MAIN_CONTENT}
            </div>
        </div>
    </div>
</div>

<!-- BEGIN: NEWS -->

<div class="mytesti" style="width: 100%; height: auto; "><img src="http://millionairescircle.club/uploads/image/foot.png" width="100%" height="640">
</div><iframe scrolling="no" frameBorder="0" src="http://millionairescircle.club/testimonial.html" style="height:900px;width:100%; display:flex; overflow-y: hidden !important; align:center; z-index:2;" title="Iframe Example"></iframe>
<div class="container-fluid news">
    <div class="row">
        <div class="container-fluid news-head text-center">
                <span class="fa-stack fa-1">
                  <i class="fa fa-circle-thin fa-stack-2x"></i>
                  <i class="fa fa-align-justify fa-stack-1x"></i>
                </span>
            Latest News
        </div>
        <div class="container-fluid news-content">
            <div class="container">
                <div class="row">
                    {NEWS_LIST}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: NEWS -->


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->

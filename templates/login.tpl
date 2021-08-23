<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
<i class="fa fa-align-justify fa-stack-1x"></i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row" align="center">
 <div class="col-xs-12 col-sm-6 col-md-4" style="float: none;">

                            <form name='LoginForm' action='{PAGE_ACTION}' method='POST'  style="text-align: left;">
                              
                              <div class="form-group">
                                <span class='error'>{LOGIN_ERROR}</span>
                              </div>
                              <div class="form-group">
                                <input name='Username' type="text" class="form-control" placeholder="{DICT.Left_Username}">
                              </div>
                              <div class="form-group">
                                <input name='Password' type="password" class="form-control" placeholder="{DICT.Left_Password}">
                              </div>
                              <!-- BEGIN: TURING -->
                              <div class="form-group">
                                <input class='form-control' type='text' name='turing' value='' maxlength='5' style='width: 100px;  display: initial;' autocomplete='off'>&nbsp;{LOGIN_TURING_IMAGE}
                              </div>
                              <!-- END: TURING -->

                              <p><strong class="strong-type-1">* </strong><a href='forgot_password.php' >{DICT.Left_ForgotPassword}</a></p>    
                              <button type="submit" class="btn btn-form-login">{DICT.Left_LogInBut}</button>
                            <input type='hidden' name='ocd' value='login' />
                            </form>
</div>

        </div>
    </div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->

<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-comments fa-stack-1x"></i>
        </span>
        {MAIN_HEADER}
    </div>
    
    <div class="container-fluid support-center">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-8 col-md-4">
                    <div class="form-support">
                        <div class="form-login-content">
                            <p><strong class="strong-type-1"> CREATE TICKET</strong></p>
                            {MAIN_CONFIRM}
                            <form action='{MAIN_ACTION}' method='POST'> 
                              <div class="form-group">
                                <span class='error'>{MAIN_FIRST_NAME_ERROR}</span>
                                <input type="text" class="form-control" placeholder="{DICT.TN_FirstName}" name='first_name' value="{MAIN_FIRST_NAME}">
                              </div>
                              <div class="form-group">
                                <span class='error'>{MAIN_LAST_NAME_ERROR}</span>
                                <input type="text" class="form-control" placeholder="{DICT.TN_LastName}" name='last_name' value="{MAIN_LAST_NAME}">
                              </div>
                              <div class="form-group">
                                <span class='error'>{MAIN_EMAIL_ERROR}</span>
                                <input type="email" class="form-control" placeholder="{DICT.TN_EmailAddress}" name='email' value="{MAIN_EMAIL}">
                              </div>
                              <div class="form-group">
                                <span class='error'>{MAIN_SUBJECT_ERROR}</span>
                                <input type="text" class="form-control" placeholder="{DICT.TN_Subject}" name='subject' value="{MAIN_SUBJECT}">
                              </div>
                              <div class="form-group">
                                <span class='error'>{MAIN_MESSAGE_ERROR}</span>
                                <textarea class="form-control" rows="3" placeholder="{DICT.TN_Message}" name='message' >{MAIN_MESSAGE}</textarea>
                              </div>    
                              <button type="submit" class="btn btn-form-login">{DICT.TN_Submit}</button>
                            <input type='hidden' name='ocd' value='{MAIN_OCD}' />
                            </form>
                        </div>
                        <hr class="form-login-hr" />
                        <div class="form-login-content">
                            <p>
                                {DICT.TN_Text1}<br /> <a href='{MAIN_ACTION}?ocd=check' class='tooSmallLink'>{DICT.TN_CheckTicket}</a></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>  
    </div>  
      
    <div class="clearfix"></div>  


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
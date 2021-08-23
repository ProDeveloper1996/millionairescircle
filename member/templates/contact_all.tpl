<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
        
        <div style='text-align:center;'><span class='message'>{THANKS}</span></div>
        
<form action='contact.php' method='POST' name='form1' style="padding:0px;margin:0px;" onSubmit="return validateForm(this);">

<div class="form-login-content">

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Send email to :</label>
            <div class="col-sm-8">
                {CONTENT}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Email Subject :</label>
            <div class="col-sm-8">
                {EMAIL_SUBJECT}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Email Message :</label>
            <div class="col-sm-8">
                {EMAIL_MESSAGE}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                {CHOOSE}
            </div>
        </div>    
    </div>                              

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                <button type='submit'  class='btn btn-form-type-3' onClick="return confirm ('Are you sure?');"><i class="fa fa-check"></i>  Send  </button>
                <button type='button'  class='btn btn-form-type-3' onClick="window.location.href='myaccount.php'">Cancel</button>
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Substitutions<i style='font-weight:normal;'>(use them in the message body)<i></label>
            <div class="col-sm-8">
                {CHANGE_TEMPLATE}
            </div>
        </div>    
    </div>                              



</div>

                
    <input type='hidden' name='ocd' value='send_email'>

</form>           



{FILE{FOOTER_TEMPLATE}}

<!-- END: MAIN -->
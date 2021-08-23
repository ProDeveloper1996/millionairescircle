<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
        
<div style='text-align:center;'>{MAIN_CONFIRM}</div>
           
<form action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <span class='error'>{MAIN_SUBJECT_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.TN_Subject} :</label>
            <div class="col-sm-8">
                {MAIN_SUBJECT}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <span class='error'>{MAIN_MESSAGE_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.TN_Message} :</label>
            <div class="col-sm-8">
                {MAIN_MESSAGE}
            </div>
        </div>    
    </div>                              
                            

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.TN_Submit}</button>
                    <button type="button" class="btn btn-form-cancel" onClick="window.location.href='{MAIN_CANCEL_URL}'">{DICT.TN_Cancel}</button>
            </div>
        </div>    
    </div>                              

</div>

    <input type='hidden' name='ocd' value='{MAIN_OCD}' />
</form>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
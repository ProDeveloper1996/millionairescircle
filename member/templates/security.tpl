<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

            <div style='text-align:center;'>
            <span class='message'>{MAIN_MESSAGE}</span>
            </div>
            
            <form action='{MAIN_ACTION}' method='POST'>
        <FIELDSET style='width:80%;'> 
            <LEGEND style='color:#ffffff;font-size:12px;'>&nbsp;Security Settings&nbsp;</LEGEND>

<div class="form-login-content">

    <div class="form-group">
        <div class="row">
            <span class='error'>{USERNAME_ERROR}</span>
            <label class="col-sm-4 control-label">Your username :</label>
            <div class="col-sm-8">
                {USERNAME}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <span class='error'>{PASSWORD1_ERROR}</span>
            <label class="col-sm-4 control-label">Your new password :</label>
            <div class="col-sm-8">
                {PASSWORD1}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <span class='error'>{PASSWORD2_ERROR}</span>
            <label class="col-sm-4 control-label">Your new password (confirm) :</label>
            <div class="col-sm-8">
                {PASSWORD2}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <span class='error'>{PASSWORD_ERROR}</span>
            <label class="col-sm-4 control-label">Current Password :</label>
            <div class="col-sm-8">
                {PASSWORD}
            </div>
        </div>    
    </div>                              

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> Update</button>
                    <button type="button" class="btn btn-form-cancel" onClick="window.location.href='myaccount.php?accesssettings'"> Cancel </button>
            </div>
        </div>    
    </div>                              

</div>

                    <input type='hidden' name='ocd' value='update' />
        </form>
</fieldset>   

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
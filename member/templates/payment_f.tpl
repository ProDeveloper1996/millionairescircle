<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}


<h2>{MAIN_HEADER}</h2>
        
        <div style='text-align:center;'><span class='error'>{MAIN_MESSAGE}</span></div>

        <p class='text'>{DESCRIPTION}</p>
        
        <form name='account' action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Select level :</label>
            <div class="col-sm-8">
                {LEVEL}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Select processor :</label>
            <div class="col-sm-8">
                {PROCESSOR}
            </div>
        </div>    
    </div>                              

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> Preview</button>
            </div>
        </div>    
    </div>                              

</div>

                        <input type='hidden' name='ocd' value='prepayment' />
            </form>           


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
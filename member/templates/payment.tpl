<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
        
            
          <form name='account' action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Amount :</label>
            <div class="col-sm-8">
                {CURRENCY}{AMOUNT}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Select Wallet :</label>
            <div class="col-sm-8">
                {PROCESSOR}
            </div>
        </div>    
    </div>                              
                            
<!--
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> Preview</button>
            </div>
        </div>    
    </div>                              
-->
</div>

                         <input type='hidden' name='prepayment' value='1' />
           </form>   
               
    
    <!-- BEGIN: PREPAYMENT -->
<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>{ROW_PROCESSOR} payment form</b></label>
            <div class="col-sm-8">
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Payment for:</b></label>
            <div class="col-sm-8">{ROW_PRODUCT_NAME}</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Amount:</b></label>
            <div class="col-sm-8">{CURRENCY}{ROW_AMOUNT}</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Wallet fee:</b></label>
            <div class="col-sm-8">{ROW_FEE}%</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Total amount:</b></label>
            <div class="col-sm-8">{CURRENCY}{ROW_FULL_SUM}</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">{ROW_CODE}</div>
        </div>    
    </div>

</div>

                  
    <!-- END: PREPAYMENT -->
    
    <!-- BEGIN: PREPAYMENT_NO -->
            {NO_RIGHT}          
    <!-- END: PREPAYMENT_NO -->
        

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
            
          <form name='account' action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Amount :</label>
            <div class="col-sm-8">
                {AMOUNT}
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Select person :</label>
            <div class="col-sm-8">
                {MEMBERS}
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

                        <input type='hidden' name='prepayment' value='1' />
            </form>
            

    <!-- BEGIN: PREPAYMENT -->
<table width="60%" cellpadding="0" cellspacing="0" border='0' align='center'>
     <tr>
        <td span='2' align='center' class="w_padding">
            <span class='question'><b>{ROW_PROCESSOR} payment form</b></span>
        </td>
    </tr>
    <tr>
        <td class="w_padding">


            <table width="100%" cellpadding="1" cellspacing="0" border='0'>
                <tr style='height:7px;'><td colspan='2' class="w_padding"></td></tr>
                
                <tr><td class="w_padding"><span class='question'> Payment for</span></td><td class="w_padding">{ROW_MEMBER}</td></tr>
                
                <tr><td class="w_padding"><span class='question'> Product</span></td><td class="w_padding">{ROW_PRODUCT_NAME}</td></tr>

                <tr><td class="w_padding"><span class='question'> Processor fee</span></td><td class="w_padding">{ROW_FEE}%</td></tr>
                <tr><td class="w_padding"><span class='question'> Amount to pay</span></td><td class="w_padding">{CURRENCY}{ROW_AMOUNT}</td></tr>

                <tr style='height:7px;'><td class="w_padding" colspan='2'></td></tr>
                <tr><td colspan='2' align='left' class="w_padding">{ROW_CODE}</td></tr>
                <tr style='height:4px;'><td class="w_padding" colspan='2'></td></tr>
            </table>

        </td>
    </tr>
</table>
                   
    <!-- END: PREPAYMENT -->
    
    <!-- BEGIN: PREPAYMENT_NO -->
            {NO_RIGHT}          
    <!-- END: PREPAYMENT_NO -->            

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
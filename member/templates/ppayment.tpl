<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
            
          <form name='account' action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <div class="col-sm-8">
                {DESCRIPTION}
            </div>
        </div>    
        <div class="row">
            <div class="col-sm-8">
                {PHOTO}
            </div>
        </div>    
    </div>      
    <div class="form-group">
    </div>      

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Title:</label>
            <div class="col-sm-8">
                {TITLE}
            </div>
        </div>    
    </div>      
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Amount:</label>
            <div class="col-sm-8">
                {AMOUNT}&nbsp;&nbsp;{DOWNLOAD}
            </div>
        </div>    
    </div>      
    {BUTTON}
</div>      

<!--
            <table width="100%" cellpadding="2" cellspacing="0" border='0'>
                <tr>
                    <td colspan='2'>
                        {DESCRIPTION}
                    </td>
                </tr>
                <tr style="height:10px;"><td colspan='2'></td></tr>
                <tr>
                    <td valign='top' style='padding-right:5px;'>
                        {PHOTO}
                    </td>
                    <td valign='top' width="100%">     
                        <table width="100%" cellpadding="4" cellspacing="0" border='0'>
                            <tr>
                                <td style='padding:2px;'>
                                    <span class='question'>Title :</span>
                                </td>
                                <td style='padding:2px;'>
                                    <span class='question'>{TITLE}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:2px;'>
                                    <span class='question'>Amount :</span>
                                </td>
                                <td style='padding:2px;'>
                                    <span class='question' style='color:#d6912c;'>{AMOUNT}&nbsp;&nbsp;{DOWNLOAD}</span>
                                </td>
                            </tr>
                            
                            {BUTTON}
                        </table>
                    </td>
                </tr>
            </table>
-->
           <input type='hidden' name='prepayment' value='1'>
            </form>   
               
    <!-- BEGIN: PREPAYMENT -->

<!--            
<table width="60%" cellpadding="0" cellspacing="0" border='0' align='center' bgcolor="#8db0d8" style='margin-top:10px;border:1px solid #accaec;'>
    <tr>
        <td class="w_padding" style='background-color:#6383a6;'>
            <span class='question'>{ROW_PROCESSOR} payment form</span>
        </td>
    </tr>
    <tr>
        <td class="w_padding">
            <table width="100%" cellpadding="1" cellspacing="0" border='0'>
                <tr style='height:7px;'><td colspan='2' class="w_padding"></td></tr>
                
                <tr><td style='width:100px;' class="w_padding"><span class='question'> Product :</span></td><td class="w_padding">{ROW_PRODUCT_NAME}</td></tr>

                <tr><td style='width:100px;' class="w_padding"><span class='question'> Sum :</span></td><td class="w_padding">{CURRENCY}{ROW_AMOUNT}</td></tr>

                <tr><td class="w_padding"><span class='question'> Processor fee :</span></td><td class="w_padding">{ROW_FEE}%</td></tr>

                <tr><td class="w_padding"><span class='question'> Total sum :</span></td><td class="w_padding">{ROW_FULL_SUM}</td></tr>
                <tr style='height:7px;'><td class="w_padding" colspan='2'></td></tr>
                <tr><td></td><td class="w_padding" align='center'>{ROW_CODE}</td></tr>
                <tr style='height:4px;'><td class="w_padding" colspan='2'></td></tr>
            </table>

        </td>
    </tr>
</table>
-->

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
            <label class="col-sm-4 control-label"><b>Sum:</b></label>
            <div class="col-sm-8">{CURRENCY}{ROW_AMOUNT}</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Processor fee:</b></label>
            <div class="col-sm-8">{ROW_FEE}%</div>
        </div>    
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"><b>Total sum:</b></label>
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


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
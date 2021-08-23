<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
                    
<form action={MAIN_ACTION} method='POST' onsubmit="return confirm ('Please confirm your Withdrawal Request?');" >

<div class="form-login-content">

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Total Amount :</label>
            <div class="col-sm-8">
                <b>{CURRENCY}{MAIN_CASH}</b>
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Processor :</label>
            <div class="col-sm-8">
                {PROCESSOR}
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Plain Admin Fee :</label>
            <div class="col-sm-8">
                {_CURRENCY}{WITHDRAWAL_VALUE}{FEE}
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Min sum for Cash Out :</label>
            <div class="col-sm-8">
                {CURRENCY}{MIN_CASH_OUT}
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <span class='error'>{CASH_ERROR}</span>
            <label class="col-sm-4 control-label">Amount for withdrawal:</label>
            <div class="col-sm-8">
                {CURRENCY}{CASH}
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    {BUTTON}
            </div>
        </div>    
    </div>                              

</div>

    <input type='hidden' name='ocd' value='cash_out' />
    <input type='hidden' name='processor_id' value='{PROCESSOR_ID}' />
    <input type='hidden' name='account_id' value='{ACCOUNT_ID}' />
<form>
   

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
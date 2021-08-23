<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<h2 style="text-align:center">{MAIN_HEADER} : {FIRSTNAME} {LASTNAME}</h2>
<div class="col-md-5">
</div>
<div class="col-md-4">

    <form name='activation' method='POST' action='{MAIN_ACTION}'>
        <div class="form-group">
            {MESSAGE}
        </div>
        <div class="form-group">
            <label style="width: 80px">Username: </label>
            <span class="error"></span>
            {USERNAME}
        </div>
        <div class="form-group">
            <label style="width: 80px">Password: </label>
            <span class="error"></span>
            {PASSWORD}
        </div>
        <div class="form-group">
            <button type="submit" style="width: 120px;" class="btn btn-form-login">Activate!</button>
        </div>


        <input type='hidden' name='ocd' value='update'/>
        <input type='hidden' name='i' value='{ID}'>
    </form>
</div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
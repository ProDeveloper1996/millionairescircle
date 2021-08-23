<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>


<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<div class="form-login-content">

    <div class="form-group">
        <div class="row">
            <span class='error'>{MAIN_TITLE_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.TD_Title}</label>
            <div class="col-sm-8">
                {MAIN_TITLE} <span class='message'>{DICT.TD_Title1}</span>
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">{DICT.TD_Description}</label>
            <div class="col-sm-8">
                {MAIN_DESCRIPTION1} <span class='message'>{DICT.TD_Description1}</span>
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">{DICT.TD_Description2}</label>
            <div class="col-sm-8">
                {MAIN_DESCRIPTION2}  <span class='message'>{DICT.TD_Description3}</span>
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <span class='error'>{MAIN_URL_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.TD_URL}</label>
            <div class="col-sm-8">
                {MAIN_URL} <span class='message'>{DICT.TD_URL1}</span>
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <span class="error" id="error"></span>
            <label class="col-sm-4 control-label">{DICT.TD_ShowURLinZone}</label>
            <div class="col-sm-8">
                <input type='checkbox' name='show_url' value='1' {MAIN_CHECKED} />
            </div>
        </div>    
    </div> 

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.TD_Update}</button>
                    <button type="button" class="btn btn-form-cancel" onClick="window.location.href='{MAIN_CANCEL_URL}'">{DICT.TD_Cancel}</button>
            </div>
        </div>    
    </div>                              

</div>

<input type='hidden' name='ocd' value='{MAIN_OCD}'>
<input type='hidden' name='id' value='{MAIN_ID}'>
</form>



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
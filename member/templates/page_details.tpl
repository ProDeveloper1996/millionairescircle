<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}


<h2>{MAIN_HEADER}</h2>

<form name='account' action='{MAIN_ACTION}' method='POST'>
    <div class="form-login-content">
        <div class="form-group" style="margin-bottom: 0; ">
            <div class="row">
                <span class='error'>{MAIN_TITLE_ERROR}</span>
                <label class="col-sm-4 control-label">{DICT.Page_PageTitle} :</label>
                <div class="col-sm-8">
                    {MAIN_TITLE}
                </div>
            </div>    
        </div>                              
        <div class="form-group" style="margin-bottom: 0; "> 
            <div class="row">
                <span class='error'>{MAIN_TITLE_MENU_ERROR}</span>
                <label class="col-sm-4 control-label">{DICT.Page_NameMenu} :</label>
                <div class="col-sm-8">
                    {MAIN_TITLE_MENU}
                </div>
            </div>    
        </div>                              
        <div class="form-group" style="margin-bottom: 0; "> 
            <div class="row">
                <label class="col-sm-4 control-label">{DICT.Page_Content} :</label>
            </div>    
            <div class="row">
                    {MAIN_CONTENT}
            </div>    
        </div>                              


        <div class="form-group" style="margin-bottom: 0; ">
            <div class="row">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-8">
                        <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.Page_Update}</button>
                        <button type="button" class="btn btn-form" onClick="window.location.href='{MAIN_CANCEL_URL}'"><i class="fa fa-check"></i> {DICT.Page_Cancel}</button>
                </div>
            </div>    
        </div>                              
    </div>
    <input type='hidden' name='ocd' value='{MAIN_OCD}'>
    <input type='hidden' name='id' value='{MAIN_ID}'>
</form>   

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
            
   <form name='account' action='{MAIN_ACTION}' method='POST'>

<div class="form-login-content">
    <div class="form-group">
        <div class="row">
            <span class='error'>{ACCOUNT_REPLICA_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.Page_UrlofMySite} :</label>
            <div class="col-sm-8">
                {ACCOUNT_SITE_URL}{ACCOUNT_REPLICA_URL}/
            </div>
        </div>    
    </div>                              
    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">Show My Site :</label>
            <div class="col-sm-8">
                {ACCOUNT_IS_REPLICA}
            </div>
        </div>    
    </div>                              
                            

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.Page_Update}</button>
            </div>
        </div>    
    </div>                              

</div>

      <input type='hidden' name='ocd' value='addsite' />
     </form>   




{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
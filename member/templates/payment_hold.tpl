<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}


<h2>{MAIN_HEADER}</h2>

<div style='text-align:center;'><span class='error'>{MAIN_MESSAGE}</span></div>

<p class='text'>{DESCRIPTION}</p>

<form name='account' action='{MAIN_ACTION}' method='POST'>
    You can not upgrade on Pre-Launch.<br />
    Inform me about Launch <input type="checkbox" name="inform" {CHECKED}> <br />
    (Notification email will be sent to your email address informing you about system start<br />

    <div class="form-login-content">
        <div class="form-group">
            <div class="row">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> Inform</button>
                </div>
            </div>
        </div>

    </div>

    <input type='hidden' name='ocd' value='inform'/>
</form>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
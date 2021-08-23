<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

        <span class='message'>{MAIN_CONFIRM}</span> 
        
        <FIELDSET class="block">
            <LEGEND >{DICT.PT_NewBannerForm}</LEGEND>
            <form style='padding:2px;margin:2px;' name='ptools' action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>

<div class="form-login-content">

    <div class="form-group">
        <div class="row">
            <span class='error'>{TITLE_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.PT_Title} :</label>
            <div class="col-sm-8">
                <input type='text' name='title' value='{TITLE}' maxlength='250' style='width: 100%;' />
            </div>
        </div>    
    </div>                              
    <div class="form-group">

        <div class="row">
            <span class='error'>{IMAGE_ERROR}</span>
            <label class="col-sm-4 control-label">{DICT.PT_Image} :</label>
            <div class="col-sm-8">
                <input type='file'  name='photo' value='' style='width: 100%;' />
            </div>
        </div>    
    </div>                              

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label">{DICT.PT_SelectPageForPromotion} :</label>
            <div class="col-sm-8">
                {PAGESELECT}
            </div>
        </div>    
    </div>                              

    <div class="form-group">
        <div class="row">
            <label class="col-sm-4 control-label"></label>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.PT_AddBanner}</button>
                    <input type='hidden' name='ocd' value='add' />
            </div>
        </div>    
    </div>                              

</div>
            </form>
            </fieldset>
            
<table width='100%' border='0' cellspacing='0' cellpadding='4' class="simple-little-table">
        <tr>
        <th>Image</th>
        <th>HTML Code to embed</th>
        <th>Action</th>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='40%'>{ROW_OBJECT}</td>
        <td width='50%'><textarea style='height:100%;width:100%;' name='some'>{ROW_OBJECT}</textarea></td>
        <td>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='3' align='center'>{DICT.PT_ListEmpty}</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

<div class="form-group" style="margin-top:20px">
    <div class="row">
        {MAIN_PAGES}
    </div>    
</div>                              


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
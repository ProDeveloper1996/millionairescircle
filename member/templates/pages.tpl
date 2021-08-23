<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>


<form name='account' action='{MAIN_ACTION}' method='POST'>
    <div class="form-login-content">
        <div class="form-group" style="margin-bottom: 0; ">
            <div class="row">
                <label class="col-sm-4 control-label">Your site url :</label>
                <div class="col-sm-8">
                    {REPLICA}
                </div>
            </div>    
        </div>                              
        <div class="form-group" style="margin-bottom: 0; "> 
            <div class="row">
                <label class="col-sm-4 control-label">{DICT.Page_ShowMySite} :</label>
                <div class="col-sm-8">
                    {ACCOUNT_IS_REPLICA}
                </div>
            </div>    
        </div>                              
        <div class="form-group" style="margin-bottom: 0; ">
            <div class="row">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-8">
                        <button type="submit" class="btn btn-form"><i class="fa fa-check"></i> {DICT.Page_Update}</button>
                </div>
            </div>    
        </div>                              
    </div>
    <input type='hidden' name='ocd' value='updatestatus' />
</form>   

<div align="right">{MAIN_ADDLINK}</div>
            
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table" >
    <tr>
        <th width='30'><b>{HEAD_ORDER}</b></th>
        <th>{HEAD_NAME}</th>
        <th>{HEAD_TITLE}</th>
        <th width='80' colspan='4'>{DICT.Page_Actions}</th>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ORDER}</td>
        <td>{ROW_TITLE}</td>
        <td>{ROW_MENU}</td>
        <td width='20'>{ROW_ORDERLINK}</td>
        <td width='20'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='20'>{ROW_EDITLINK}</td>
        <td width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='7'>{DICT.Page_ListEmpty}</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr style='height:5px;'><td></td></tr>
    <tr style='height:9px;'><td class='dotted'></td></tr>
    <tr><td align='right'> {MAIN_PAGES} &nbsp; </td></tr>
    <tr><td height='12'></td></tr>
</table>       


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
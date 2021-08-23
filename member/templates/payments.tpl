<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

<form>            
        <div class="filter-block">
            <div class="col-xs-12 col-sm-1 col-md-1">                                    
                <div class="row text-center">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
           {MAIN_FILTER}
            <div class="col-xs-12 col-sm-2 col-md-1">                                    
                <div class="row text-center">
                    <button type='submit'  class="btn btn-form-type-5"><i class="fa fa-check"></i></button>
                    <input type='hidden' name='filter' value=1>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
</form>

   <table width='100%' border='0' cellspacing='0' cellpadding='4' class="simple-little-table" style='margin-top:10px;'>
    <tr>
        <th>{HEAD_DATE}</th>
        <th>{HEAD_AMOUNT}</th>
        <th>{HEAD_TRANSACTION_ID}</th>
        <th>{HEAD_PROCESSOR}</th>

    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_DATE}</td>
        <td>{CURRENCY}{ROW_AMOUNT}</td>
        <td>{ROW_TRANSACTION_ID}</td>
        <td>{ROW_PROCESSOR}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='b_border' colspan='4' align='center'>{DICT.PM_ListEmpty}</td>
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
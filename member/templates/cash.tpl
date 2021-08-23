<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

<form action={MAIN_ACTION}?pg=0 method='POST'>

    <div class="filter-block">
        <div class="row" style="margin-bottom:10px">
            <div class="col-xs-12 col-sm-1 col-md-1"></div>
            <div class="col-xs-12 col-sm-11 col-md-11">
                {DICT.Cash_Text1} <input type='text' name='member_id' value='{MEMBER_ID}' style='width:60px;'
                                         maxlength='6' class='form-control'>
            </div>
        </div>

        <div class="col-xs-12 col-sm-1 col-md-1">
            <div class="row text-center">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        {MAIN_FILTER}
        <div class="col-xs-12 col-sm-2 col-md-1">
            <div class="row text-center">
                <button type='submit' class="btn btn-form-type-5"><i class="fa fa-check"></i></button>
                <input type='hidden' name='filter' value=1>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

</form>

<table width='100%' border='0' cellspacing='0' cellpadding='4' class="simple-little-table" style='margin-top:10px;'>
    <tr bgcolor='#475567'>
        <th class='b_border' align='center'>{HEAD_DESCRIPTION}</th>
        <th class='b_border' align='center'>{HEAD_AMOUNT}</th>
        <th class='b_border' align='center'>{HEAD_DATE}</th>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td class='b_border' style='text-align:left;'>{ROW_DESCRIPTION}</td>
        <td class='b_border'>{CURRENCY}{ROW_AMOUNT}</td>
        <td class='b_border'>{ROW_DATE}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='b_border' colspan='4' align='center'>{DICT.Cash_ListEmpty}</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
    {TOTAL_CASH} {CASH_SELECT}

</table>

<div class="form-group" style="margin-top:20px">
    <div class="row">
        {MAIN_PAGES}
    </div>
</div>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
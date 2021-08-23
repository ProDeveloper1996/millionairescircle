<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr>
        <td align='left'>
            <form action={MAIN_ACTION}?pg=0 method='POST'>
            &nbsp; {DICT.TN_Status}: {STATUS}
            <input type='hidden' name='filter2' value=1>
            </form>
        </td>
    </tr>
    <tr><td height='12' colspan='2'></td></tr>
</table>
            
<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center' class="simple-little-table" style='margin-top:10px;'>
    <tr bgcolor='#475567'>
        <th class='b_border' width='140' align='center'>{HEAD_DATE}</th>
        <th class='b_border' align='center'><b class='pages'>{HEAD_FEE}</b></th>
        <th class='b_border' align='center'>{HEAD_AMOUNT}</th>
        <th class='b_border' align='center' width='130'>{HEAD_STATUS}</th>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td class='b_border' width='140'>{ROW_DATE} </td>
        <td class='b_border' align='center'>{_CURRENCY}{ROW_FEE} </td>
        <td class='b_border' align='center'>{CURRENCY}{ROW_AMOUNT} </td>
        <td class='b_border' align='center' width='130'>{ROW_STATUS} </td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='b_border' colspan='4' align='center'>{DICT.TN_ListEmpty}</td>
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
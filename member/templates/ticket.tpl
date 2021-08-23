<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr>
        <td align='left'>
            <form name='sel' action='{MAIN_ACTION}' method='POST'>
            <table border='0' cellspacing='0' cellpadding='2'>
                <tr><td>{ACTIVE_STATUS_FILTER}&nbsp;</td></tr>
                <input type='hidden' name='pg' value=0>
            </table>
            </form>    
        </td>
        <td  style='text-align:right;'>
        {MAIN_ADDLINK} &nbsp; </td>
    </tr>
    <tr><td colspan='2' style='text-align:center;'>{MAIN_MES}</td></tr>
</table>

<table width='100%' cellspacing='0' cellpadding='5' align='center' class="simple-little-table" >
    <tr bgcolor='#475567'>
        <th class='b_border' align='center'>{HEAD_ID}</th>
        <th class='b_border' align='center'>{HEAD_SUBJECT}</th>
        <th class='b_border' align='center'>{HEAD_DATA_CREATE}</th>
        <th class='b_border' align='center'>{HEAD_LAST_UPDATE}</span></th>
        <th class='b_border' align='center'><b class='pages'>{HEAD_LAST_REPLIER}</b></th>
        <th class='b_border' width='60' align='center' colspan='3'><b class='pages'>{DICT.TN_Actions}</b></th>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td align='center' class='b_border'>{ROW_ID}</td>
        <td class='b_border' style='text-align:left;'>{ROW_SUBJECT}</td>
        <td class='b_border'>{ROW_DATA_CREATE}</td>
        <td class='b_border'>{ROW_LAST_UPDATE}</td>
        <td class='b_border'>{ROW_LAST_REPLIER}</td>
        <td class='b_border' align='center' width='20'>{ROW_ACTIVELINK}</td>
        <td class='b_border' align='center' width='20'>{ROW_EDITLINK}</td>
        <td class='b_border' align='center' width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='b_border' colspan='8' align='center'>{DICT.TN_ListEmpty}</td>
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
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td></tr>
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
    <tr><td height='12'></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' bgcolor='#F5F5F5' class='w_border'>
    <tr>
        <td class='w_border' align='center' width='50'><b>{HEAD_SEC}</b></td>
        <td class='w_border' align='center'><b>{HEAD_AUTHOR}</b></td>
        <td class='w_border' align='center'><b>{HEAD_DESCRIPTION}</b></td>
        <td class='w_border' align='center' colspan='4' width='80'><b>Actions</b></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' valign='top' valign='top' align='center'>{ROW_SEC}</td>   
        <td class='w_border' valign='top' valign='top'>{ROW_AUTHOR}</td>
        <td class='w_border' valign='top'>{ROW_DESCRIPTION}</td>
        <td class='w_border' align='center' width='20' valign='top'>{ROW_ORDER}</td>
        <td class='w_border' align='center' width='20' valign='top'>{ROW_ACTIVELINK}</td>
        <td class='w_border' align='center' width='20' valign='top'>{ROW_EDITLINK}</td>
        <td class='w_border' align='center' width='20' valign='top'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='7' align='center'>The list is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='12'></td></tr>
    <tr><td align='right'> {MAIN_PAGES} &nbsp; </td></tr>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
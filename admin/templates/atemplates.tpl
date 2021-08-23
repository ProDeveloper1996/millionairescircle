<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4><hr></td></tr>
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
    <tr><td height='5'></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='4' bgcolor='#F5F5F5' class='w_border'>
    <tr>
        <td class='w_border_h' align='center'>ID</td>
        <td class='w_border_h' align='center'>{HEAD_SUBJECT}</td>
        <td class='w_border_h' width='75' colspan='3' align='center'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td  align='center' class='w_border'>{ROW_ID}</td>
        <td class='w_border'>{ROW_SUBJECT}</td>
        <td class='w_border' align='center' width='20'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td class='w_border' align='center' width='20'>{ROW_EDITLINK}</td>
        <td class='w_border' align='center' width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='4' align='center'>The list is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{FILE{FOOTER_TEMPLATE}}

<!-- END: MAIN -->
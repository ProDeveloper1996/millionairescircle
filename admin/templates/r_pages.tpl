<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td align='left'>Replicated Site URL: {R_SITE_URL} </td><td align='right'>{MAIN_ADDLINK} &nbsp;</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' bgcolor='#F5F5F5' class="simple-little-table">
    <tr>
        <td width='30'><b>{HEAD_ORDER}</b></td>
        <td align='center'><b>{HEAD_NAME}</b></td>
        <td align='center'><b>{HEAD_TITLE}</b></td>
        <td width='80' colspan='4' align='center'><b>Actions</b></td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td align='center'>{ROW_ORDER}</td>
        <td >{ROW_TITLE}</td>
        <td >{ROW_MENU}</td>
        <td align='center' width='24'>{ROW_ORDERLINK}</td>
        <td align='center' width='24'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td align='center' width='24'>{ROW_EDITLINK}</td>
        <td align='center' width='24'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td colspan='7' align='center'>The list of Replicated site pages is empty</td>
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
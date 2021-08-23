<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="You can administer (add/edit/delete) Landing Pages in your system using this page" class="vtip"><img src='./images/question.png'></span></td></tr>
</table>
    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <td align='right'> {MAIN_ADDLINK}</td></tr>
    <tr><td height='12'></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td width='10%' align='center'>{HEAD_DATE}</td>
        <td align='center'>{HEAD_TITLE}</td>
        <td align='center' width='80' colspan='4'>Actions</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td align='center' valign='top'>{ROW_DATE}&nbsp;</td>
        <td valign='top'>{ROW_TITLE}</td>
        <td align='center' width='24' valign='middle'>{ROW_LANDLINK}</td>
        <td align='center' width='24' valign='middle'>{ROW_ACTIVELINK}</td>
        <td align='center' width='24' valign='middle'>{ROW_EDITLINK}</td>
        <td align='center' width='24' valign='middle'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td colspan='6' align='center'>No Landing Pages were created</td>
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
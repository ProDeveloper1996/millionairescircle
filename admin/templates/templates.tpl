<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<table width='100%'  border="0" cellpadding="0" cellspacing="0"  class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="List of email templates automatically sent on certain actions of members. You can edit/deactivate all the messages." class="vtip"><img src='./images/question.png'></span></td></tr>
</table>
<table  border="0" cellpadding="0" cellspacing="0">
    <tr><td align='center'><span class='error'>{MESSAGE}</span><td></tr>
</table>

<table width='100%' border='0' cellspacing='0' class="simple-little-table">
    <tr>
        <td>ID</td>
        <td>{HEAD_DESCRIPTION}</td>
        <td colspan='2'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ID}</td>
        <td>{ROW_DESCRIPTION}</td>
        <td width='20'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='20'>{ROW_EDITLINK}</td>
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
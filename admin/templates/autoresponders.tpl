<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<table  border="0" cellpadding="0" cellspacing="0" width='100%' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="List of email templates automatically sent to 
      the members (to those who have not activated their account for inactive members autoresponder/to those who activated their account for active members autoresponder in certain period of time you set." class="vtip">
      <img src='./images/question.png'></span></td></tr>
</table>
<table  border="0" cellpadding="0" cellspacing="0" width='100%'>
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
    <tr><td align='center'><span class='error'>{MESSAGE}</span><td></tr>
</table>

<table width='100%' border='0' cellspacing='0'  class="simple-little-table">
    <tr>
        <td>ID</td>
        <td>{HEAD_SUBJECT}</td>
        <td>{HEAD_DAYS}</td>
        <td colspan='3'>Action</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ID}</td>
        <td>{ROW_SUBJECT}</td>
        <td>{ROW_DAYS}</td>
        <td width='20'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='20'>{ROW_EDITLINK}</td>
        <td width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='6' align='center'>The list of templates is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{FILE{FOOTER_TEMPLATE}}

<!-- END: MAIN -->
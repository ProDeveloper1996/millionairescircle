<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="List of tickets written by your visitors in public area." class="vtip"><img src='./images/question.png'></span></td></tr>
</table>

<form action={MAIN_ACTION} method='POST'>
<table border='0' cellspacing='0' cellpadding='3'  class='filter'>
    <tr>
        <td width='50%'>Show: {ACTIVE_STATUS_FILTER}&nbsp; Ticket ID {ID_FILTER}</td>
        <td><input type='submit' class='some_btn' value=" Apply "></td>
    </tr>
    <input type='hidden' name='pg' value=0>
</table>
</form>

<table width='100%' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td>{HEAD_NAME}</td>
        <td>{HEAD_EMAIL}</td>
        <td>{HEAD_SUBJECT}</td>
        <td>{HEAD_TICKET_CODE}</td>
        <td>{HEAD_DATA_CREATE}</td>
        <td>{HEAD_LAST_UPDATE}</td>
        <td colspan='3'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ID}</td>
        <td>{ROW_NAME}</td>
        <td>{ROW_EMAIL}</td>
        <td>{ROW_SUBJECT}</td>
        <td>{ROW_TICKET_CODE}</td>
        <td>{ROW_DATA_CREATE}</td>
        <td>{ROW_LAST_UPDATE}</td>
        <td width='20'>{ROW_ACTIVELINK}</td>
        <td width='20'>{ROW_EDITLINK}</td>
        <td width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='10' align='center'>No tickets from your visitors were created yet.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td height='12'></td></tr>
    <tr><td align='right'> {MAIN_PAGES} &nbsp; </td></tr>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
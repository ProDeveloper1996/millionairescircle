<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<form action={MAIN_ACTION} method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' class='filter'>
    <tr><td>Filter</td></tr>
    <tr>
        <td>Show: {ACTIVE_STATUS_FILTER} Ticket ID {ID_FILTER}</td>
        <td><input type='submit' class='some_btn' value=" Apply "></td>
    </tr>
    <input type='hidden' name='pg' value=0>
</table>
</form>

<table width='100%' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td>{HEAD_NAME}</td>
        <td>{HEAD_SUBJECT}</td>
        <td>{HEAD_DATA_CREATE}</td>
        <td>{HEAD_LAST_UPDATE}</td>
        <td>{HEAD_LAST_REPLIER}</td>
        <td colspan='3'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='3%'>{ROW_ID}</td>
        <td>{ROW_MEMBER}</td>
        <td>{ROW_SUBJECT}</td>
        <td>{ROW_DATA_CREATE}</td>
        <td>{ROW_LAST_UPDATE}</td>
        <td>{ROW_LAST_REPLIER}</td>
        <td width='3%'>{ROW_ACTIVELINK}</td>
        <td width='3%'>{ROW_EDITLINK}</td>
        <td width='3%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='10' align='center'>No tickets from your members were created yet.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>     
<table width='100%' border='0' cellspacing='0' cellpadding='0'>     
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_DATE}</td>
        <td>{HEAD_TITLE}</td>
        <td>{HEAD_DESTINATION}</td>
        <td colspan='3'>Action</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='10%'>{ROW_DATE}&nbsp;</td>
        <td>{ROW_TITLE}</td>
        <td width='10%'>{ROW_DESTINATION}</td>
        <td width='3%'>{ROW_ACTIVELINK}</td>
        <td width='3%'>{ROW_EDITLINK}</td>
        <td width='3%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='6' align='center'>The list of news is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
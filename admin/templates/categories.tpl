<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr></table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td align='center'><span class='error'>{MESSAGE}</span><td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='4' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td>{HEAD_TITLE}</td>
        <td>{HEAD_LEVEL}</td>
        <td colspan='3'>Action</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ID}</td>
        <td>{ROW_TITLE}</td>
        <td>{ROW_LEVEL}</td>
        <td width='5%'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='5%'>{ROW_EDITLINK}</td>
        <td width='5%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='6'>The list of E-Shop categories is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{FILE{FOOTER_TEMPLATE}}

<!-- END: MAIN -->
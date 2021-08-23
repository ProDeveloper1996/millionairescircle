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
        <td>{HEAD_ID}</td>
        <td>{HEAD_QUESTION}</td>
        <td>{HEAD_ANSWER}</td>
        <td colspan='3'>Actions</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='3%'>{ROW_ID}&nbsp;</td>
        <td>{ROW_QUESTION}</td>
        <td>{ROW_ANSWER}</td>
        <td width='3%'>{ROW_ACTIVELINK}</td>
        <td width='3%'>{ROW_EDITLINK}</td>
        <td width='3%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='6' align='center'>The list of FAQ is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
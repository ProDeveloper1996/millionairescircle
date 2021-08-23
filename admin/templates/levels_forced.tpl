<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}<span title="You can administer (add/edit/delete) levels in your system using this page." class="vtip"><img src='./images/question.png'></span></td>
    </tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td align='right'> {MAIN_ADDLINK}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_NUMBER}</td>
        <td>{HEAD_TITLE}</td>
        <td>{HEAD_COST}</td>
        <td>{HEAD_DEPTH}</td>
        <td colspan='3'><b>Actions</b></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_NUMBER}</td>
        <td>{ROW_TITLE}</td>
        <td>{CURRENCY}{ROW_COST}</td>
        <td>{ROW_DEPTH}</td>
        <td width='5%'>{ROW_ORDERLINK}</td>
        <td width='5%'>{ROW_EDITLINK}</td>
        <td width='5%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='7' align='center'>The list of levels is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
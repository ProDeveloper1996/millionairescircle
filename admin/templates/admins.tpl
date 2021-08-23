<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td height='20'>{MAIN_HEADER}</td>
  </tr>
</table>

{MAIN_ADDLINK}
<table width='100%' border='0' cellspacing='0' cellpadding='0' class="simple-little-table">
    <tr>
        <td width='100%'>Username</td>
        <td colspan='3' style='color:#2EA6E6'><b>Activity</b></td>
</tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_USERNAME}</td>
        <td>{ROW_ACTIVELINK}</td>
        <td>{ROW_EDITLINK}</td>
        <td>{ROW_DELFOREVER}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td colspan='20' align='center'>The list of members is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{MAIN_PAGES}



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
            
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="You can create extra pages for members backoffice and place the information you want to share with the members of your site.
     The link to the page will be automatically placed in the additional left section of menus" class="vtip"><img src='./images/question.png'></span></td></tr>
</table>     
<table width='100%' border='0' cellspacing='0' cellpadding='0'>     
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ORDER}</td>
        <td>{HEAD_NAME}</td>
        <td>{HEAD_TITLE}</td>
        <td>{HEAD_DESTINATION}</td>
        <td width='100' colspan='4'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ORDER}</td>
        <td>{ROW_TITLE}</td>
        <td>{ROW_MENU}</td>
        <td>{ROW_DESTINATION}</td>
        <td width='25'>{ROW_ORDERLINK}</td>
        <td width='25'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='25'>{ROW_EDITLINK}</td>
        <td width='25'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='9' align='center'>The list of Member Area Pages is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
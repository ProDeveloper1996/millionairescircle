<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}<span title="You can create extra pages for public area and place the information you want to share with the visitors of your site. The first two can not be deleted. All the other pages you created can be managed." class="vtip"><img src='./images/question.png'></span></td>
    </tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td><b>{HEAD_ORDER}</td>
        <td><b>{HEAD_NAME}</td>
        <td><b>{HEAD_TITLE}</td>
        <td><b>{HEAD_URL}</td>
        <td colspan='5'>Actions</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_ORDER}</td>
        <td>{ROW_TITLE}</td>
        <td>{ROW_MENU}</td>
        <td>{ROW_URL}</td>
        <td width='20'>{ROW_ORDERLINK}</td>
        <td width='20'><div id='resultika{ROW_ID}'>{ROW_MENULINK}</div></td>
        <td width='20'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='20'>{ROW_EDITLINK}</td>
        <td width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='8' align='center'>The list of Public Pages is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
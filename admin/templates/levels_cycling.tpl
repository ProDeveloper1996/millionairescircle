<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='4'>
    <tr><td height='20'><H4>{MAIN_HEADER}<span title="You can administer (add/edit/delete) levels in your system using this page" class="vtip"><img src='./images/question.png'></span></H4><hr></td></tr>
    <tr><td align='right'>{MAIN_ADDLINK}</td></tr>
</table>


<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_NUMBER}</td>
        <td>{HEAD_TITLE}<span title="The Titles of Levels in your system." class="vtip"><img src='./images/question.png'></span></td>
        <td>{HEAD_COST}<span title="Amount member need to pay to be placed to the matrix in this level." class="vtip"><img src='./images/question.png'></span></td>
        <td>{HEAD_HOST_FEE}<span title="Amount member is paid as soon as they complete matrix in this level." class="vtip"><img src='./images/question.png'></span></td>
        <td>{HEAD_ENR_FEE}<span title="Amount member's sponsor is paid as soon as their 1-st level referral completes matrix in this level." class="vtip"><img src='./images/question.png'></span></td>
        <td>Admin Fee<span title="" class="vtip"><img src='./images/question.png'></span></td>
        <td>{HEAD_WIDTH}<span title="The width of the matrix." class="vtip"><img src='./images/question.png'></span></td>
        <td>{HEAD_DEPTH}<span title="The depth of the matrix." class="vtip"><img src='./images/question.png'></span></td>
        <td colspan='3'><b>Actions</b></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_NUMBER}</td>
        <td>{ROW_TITLE}</td>
        <td>{CURRENCY}{ROW_COST}</td>
        <td>{CURRENCY}{ROW_HOST_FEE}</td>
        <td>{CURRENCY}{ROW_ENR_FEE}</td>
        <td>{CURRENCY}{ROW_ADMIN_FEE}</td>
        <td>{ROW_WIDTH}</td>
        <td>{ROW_DEPTH}</td>
        <td width='5%'>{ROW_ORDERLINK}</td>
        <td width='5%'>{ROW_EDITLINK}</td>
        <td width='5%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='10' align='center'>The list of levels is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
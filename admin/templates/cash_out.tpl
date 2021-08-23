<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}</td>
    </tr>
</table>

<table width='40%' border='0' cellspacing='0' cellpadding='3' align='left' class='filter'>
    <tr><td>Filter</td></tr>
    <tr>
        <td>
            <form action={MAIN_ACTION}?pg=0 method='POST'>
                Show: {STATUS}
            <input type='hidden' name='filter2' value=1>
            </form>
        </td>
        <td >
            <form action={MAIN_ACTION}?pg=0 method='POST'>
                of Member ID:&nbsp;{MAIN_FILTER} </td><td><input type='submit' class='some_btn' value=" Apply ">
            <input type='hidden' name='filter1' value=1>
            </form>
        </td>
    </tr>
</table>
<br />
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_USERNAME}</td>
        <td>{HEAD_DATE}</td>
        <td>{HEAD_FEE}</td>
        <td>{HEAD_AMOUNT}</td>
        <td>{HEAD_PROCESSOR}</td>
        <td>{HEAD_PAY}</td>
        <td colspan='2'>Actions</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_USERNAME} </td>
        <td>{ROW_DATE} </td>
        <td>{_CURRENCY}{ROW_FEE} </td>
        <td>{CURRENCY}{ROW_AMOUNT} </td>
        <td>{ROW_PROCESSOR} </td>
        <td>{ROW_PAY}</td>
        <td width='20'>{ROW_COMPLETE}</td>
        <td width='20'>{ROW_DECLINE}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='8' align='center'>No one requested for withdrawal yet</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}
<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td height='12'></td></tr>
    <tr><td align='right'>{MAIN_DELETEALLDENIED}</td></tr>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
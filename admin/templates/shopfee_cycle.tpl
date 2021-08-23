<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4><hr></td></tr>
    <tr><td height='12' align='center'>{MAIN_MESSAGE}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' bgcolor='#F1F1F1' class='w_border'>
    <tr>
        <td class='w_border_h' align='center' rowspan='2'><b>{HEAD_TITLE}</b></td>
        <td class='w_border_h' align='center'>{HEAD_SPON}</td>
        <td class='w_border_h' align='center' rowspan='2'>Action</td>
   </tr>
   <tr>
        <td class='w_border' align='center'>{HEAD_NAMES}</td>
    </tr>

    {MAIN_CONTENT}

<!--    <tr>
        <td colspan='3' align='right'>
            <form>
                <input class='some_btn' type='submit' value="Clear Fees">
                <input type='hidden' name='ocd' value='clear_fee'>
            </form>
        </td>
    </tr>
-->
</table>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
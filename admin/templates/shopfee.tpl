<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12' align='center'>{MAIN_MESSAGE}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' bgcolor='#F1F1F1' class='w_border'>
    <tr>
        <td class='w_border' align='center' rowspan='2'><b>{HEAD_TITLE}</b></td>
        <td class='w_border' align='center'><b>{HEAD_FEE}</b></td>
        <td class='w_border' align='center'><b>{HEAD_SPON}</b></td>
        <td class='w_border' align='center' rowspan='2'><b>Action</b></td>
   </tr>
   <tr>
        <td class='w_border' align='center'><b>{HEAD_NAMES}</b></td>
        <td class='w_border' align='center'><b>{HEAD_NAMES}</b></td>
    </tr>

    {MAIN_CONTENT}

    <tr style='height:30px;' bgcolor='#E7E7E7'>
        <td colspan='4' class='w_border' align='center'>
            <form>
                <input type='submit' value="Clear Fees">
                <input type='hidden' name='ocd' value='clear_fee'>
            </form>
        </td>
    </tr>
</table>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
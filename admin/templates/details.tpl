<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td>
    </tr>
    <tr>
        <td>
            <form action='{MAIN_ACTION}' method='POST'>{MAIN_DATE_SELECT}</form>
        </td>
    </tr>
</table>

          <table width="70%" cellpadding="3" cellspacing="0" border='0'>
                <tr style='height:20px;'><td colspan='2'></td></tr>
                <tr>
                    <td width="70%" style='padding-left:20px;'>
                        <span class='signs_b' style='font-size:14px;'>Report for : {SIGNS} </span>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style='height:10px;'><td colspan='2'></td></tr>
                <tr>
                    <td width="70%">
                        <span class='signs_b'>Total amount of the members payments :</span>
                    </td>
                    <td>
                        <span class='signs_b' style='font-weight:normal;'>{TOTAL_PAYMENTS}</span>
                    </td>
                </tr>
                <tr>
                    <td width="70%">
                        <span class='signs_b'>Total cash in the members payments :</span>
                    </td>
                    <td>
                        <span class='signs_b' style='font-weight:normal;'>${TOTAL_IN_PAYMENTS}</span>
                    </td>
                </tr>
                <tr style='height:10px;'><td colspan='2'></td></tr>
                <tr>
                    <td width="70%">
                        <span class='signs_b'>Total cash in active earnings :</span>
                    </td>
                    <td>
                        <span class='signs_b' style='font-weight:normal;'>${TOTAL_ACTIVE_EARNINGS}</span>
                    </td>
                </tr>
                <tr style='height:10px;'><td colspan='2'></td></tr>
                <tr>
                    <td width="70%">
                        <span class='signs_b'>Total cash in pending Cash Out :</span>
                    </td>
                    <td>
                        <span class='signs_b' style='font-weight:normal;'>${TOTAL_PENDING_CASH_OUT}</span>
                    </td>
                </tr>

                <tr>
                    <td width="70%">
                        <span class='signs_b'>Total cash in completed Cash Out :</span>
                    </td>
                    <td>
                        <span class='signs_b' style='font-weight:normal;'>${TOTAL_COMPLETED_CASH_OUT}</span>
                    </td>
                </tr>
            </table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
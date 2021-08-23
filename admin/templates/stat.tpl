<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width="100%" border='0' cellspacing='0' cellpadding='3' class="simple-little-table">
    <tr>
        <td>


            <table width="100%" cellpadding="3" cellspacing="0" class="simple-little-table">
                <tr>
                    <td width='25%' align='left'>Total amount of members:</td>
                    <td>{TOTAL_MEMBERS}</td>
                </tr>
                <tr>
                    <td>Amount of verified members:</td>
                    <td>{VERIFIED_MEMBERS}</td>
                </tr>
                <tr>
                    <td>Total amount of members in matrix (paid members):</td>
                    <td>{PAID_MEMBERS}</td>
                </tr>
                <tr>
                    <td valign='top'>Including:</td>
                    <td>{LEVELS}</td>
                </tr>
            </table>


        </td>
    </tr>
        <tr>
        <td><H3>Payment statistic</H3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" class="simple-little-table">
                <tr>
                    <td width="25%">Total amount paid by members:</td>
                    <td>{CURRENCY}{TOTAL_PAID}</td>
                </tr>
                <tr>
                    <td>Total amount of members earnings:</td>
                    <td>{CURRENCY}{TOTAL_EARNINGS}</td>
                </tr>
                <tr>
                    <td>Total amount of income deduction:</td>
                    <td>{CURRENCY}{TOTAL_WASTED}</td>
                </tr>
                <tr>
                    <td>Total amount in withdrawals:</td>
                    <td>{CURRENCY}{TOTAL_IN_CASH_OUT}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
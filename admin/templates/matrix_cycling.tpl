<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='message'>{MAIN_MESSAGE}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
        <td align='center'>
            <form name='pay_set' action='{MAIN_ACTION}' method='POST'>
            <table width='30%' align='center' border='0' cellspacing='0' cellpadding='5'>
            <tr><td>Set Entrance Fee <span title="Amount that newcomer needs to pay to be placed to the first level of your system (Usually this is the cost of the first level)." class="vtip">
            <img src='./images/question.png'></span> as {CURRENCY} {FEE} </td></tr>

            <tr><td align='center' colspan='2'>
            <input type='hidden' name='ocd' value='update_cycling'><input class='some_btn' type='submit' value='Update'></td></tr>
            </table>
            </form>
        </td>
    </tr>
</table>

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%'>
<td height='12' align='center'><span class='error'>{MAIN_MESS}</span></td></tr></table>
<form action='{MAIN_ACTION}' method='POST'>
<table width='40%' align='center' border='0' cellspacing='0' cellpadding='3'>
    <tr>
        <td colspan='2' class='ptitle'>Substract commissions:</td>
    </tr>
    <tr>
        <td>Balance:</td>
        <td>{CURRENCY}{BALANCE}</td>
    </tr>
    <tr>
        <td>Amount({CURRENCY}):</td>
        <td>{AMOUNT}</td>
    </tr>
    <tr>
        <td>Description*:</td>
        <td>{DESCRIPTION}</td>
    </tr>
    <tr height='30'>
        <td align='center' colspan='2'>
            <input class='some_btn' type='submit' value=' Substract '><br><br><input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='members.php'">
            <input type='hidden' name='ocd' value='substract'>
            <input type='hidden' name='id' value='{ID}'>
        </td>
    </tr>
</table>
</form>

<br><hr><br>
<form name='second' action='{MAIN_ACTION}' method='POST'>
<table width='40%' align='center' border='0' cellspacing='0' cellpadding='3'>
    <tr>
        <td colspan='2' class='ptitle'>Add commissions:</td>
    </tr>
    <tr>
        <td>Balance:</td>
        <td>{CURRENCY}{BALANCE}</td>
    </tr>
    <tr>
        <td>Amount({CURRENCY}):</td>
        <td>{AMOUNT}</td>
    </tr>
    <tr>
        <td>Description*:</td>
        <td>{DESCRIPTION}
        </td>
    </tr>
    <tr height='30'>
        <td align='center' colspan='2'>
            <input class='some_btn' type='submit' value=' Add '><br><br> <input type='button' class='some_btn' value='Cancel' onClick="window.location.href='members.php'">
            <input type='hidden' name='ocd' value='add'>
            <input type='hidden' name='id' value='{ID}'>
        </td>
    </tr>
</table>
</form>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td>
        <td align='right'><div class="spoiler_style" onClick="open_close('spoiler1')">
         HELP GUIDE</div></td>
    </tr>
</table>
<table width='100%'>
    <tr>
      <td><div id="spoiler1" style="display:none;">The list of cash transactions within the system (commissions, bonuses, PIF. etc.</div></td>
    </tr>
</table>

<form action={MAIN_ACTION}?pg=0 method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' class='filter'>
    <tr><td>Filter</td></tr>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='2'>
                <tr>
                    <td>Commissions of member ID - {CASH_TO_ID}&nbsp;Commissions from member ID - {CASH_FROM_ID}&nbsp;Commissions from payment ID - {CASH_PAYMENT_ID}&nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td>{MAIN_FILTER}</td>
                    <td width='10%'><input type='submit' class='some_btn' value=" Apply "></td>
                </tr>
            </table>
        </td>
        
    </tr>
    <input type='hidden' name='filter' value=1>
</table>
</form>
<br />
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_CASH_ID}</td>
        <td>{HEAD_TO}</td>
        <td>{HEAD_FROM}</td>
        <td>{HEAD_AMOUNT}</td>
        <td>{HEAD_DATE}</td>
        <td>{HEAD_DESCRIPTION}</td>
        <td width='25'></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_CASH_ID}&nbsp;</td>
        <td>{ROW_TO}</td>
        <td>{ROW_FROM}</td>
        <td>{CURRENCY}{ROW_AMOUNT}</td>
        <td>{ROW_DATE}&nbsp;</td>
        <td>{ROW_DESCRIPTION}</td>
        <td>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='7' align='center'>No cash was paid yet.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
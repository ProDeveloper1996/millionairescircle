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
      <td><div id="spoiler1" style="display:none;">The list of payments made by your members. Clicking on the members name will lead you to the members backoffice Payments History.</div></td>
    </tr>
</table> 

<form action={MAIN_ACTION}?pg=0 method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' class='filter'>
    <tr><td>Filter</td></tr>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='2'>
                <tr>
                    <td>Payments from member ID {MEMBER_ID} {MAIN_FILTER}</td>
                    <td><input type='submit' class='some_btn' value=" Apply "></td>
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
        <td>{HEAD_PAYMENT_ID}</td>
        <td>{HEAD_MEMBER_ID}</td>
        <td>{HEAD_AMOUNT}</td>
        <td>{HEAD_DATE}</td>
        <td>{HEAD_DESCRIPTION}</td>
        <td>{HEAD_PROCESSOR}</td>
        <td>{HEAD_TR}</td>
        <td width='25'></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_PAYMENT_ID}</td>
        <td>{ROW_MEMBER_ID}</td>
        <td>{CURRENCY}{ROW_AMOUNT}</td>
        <td>{ROW_DATE}</td>
        <td>{ROW_DESCRIPTION}</td>
        <td>{ROW_PROCESSOR}</td>        
        <td>{ROW_TR}</td>
        <td>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='8' align='center'>No payments were made yet.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
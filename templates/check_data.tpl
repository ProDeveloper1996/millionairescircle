<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<fieldset>
<legend><div class="corner"></div>{MAIN_HEADER}</legend>

<form action='{MAIN_ACTION}'>
<table width='80%' border='0' cellspacing='0' cellpadding='5' align='center'>
    <tr><td colspan='2' align='center' class="w_padding">{MESSAGE}</td></tr>
    <tr>
        <td width='50%' class="w_padding" style='text-align:right;'>
            <span class='question'>Enter your Username :</span>
        </td>
        <td class="w_padding">
            {USERNAME}
        </td>
    </tr>
    <tr>
        <td class="w_padding" style='text-align:right;'>
            <span class='question'>Enter your Password :</span>
        </td>
        <td class="w_padding">
            {PASSWORD}
        </td>
    </tr>
    <tr>
        <td class="w_padding" style='text-align:right;'>
            <span class='question'>Enter your pin-code :</span>
        </td>
        <td class="w_padding">
            {PIN_CODE}
        </td>
    </tr>
    <tr>
        <td class="w_padding" style='text-align:right;'>
            <span class='question'>Enter your new IP-address :</span>
        </td>
        <td class="w_padding">
            {IP_ADDRESS}
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td class="w_padding">
            <input type='submit' class='some_btn' value=" Update " />
            <input type='hidden' name='ocd' value='update' />
            <input type='hidden' name='i' value={ID} />
        </td>
    </tr>
</table>
</form>   

</fieldset>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
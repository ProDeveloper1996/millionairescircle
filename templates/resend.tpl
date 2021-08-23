<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<fieldset>
<legend><div class="corner"></div>{MAIN_HEADER}</legend>

            <form name='LoginForm' action='{PAGE_ACTION}' method='POST'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>

                <tr>
                    <td colspan='2' class="w_padding">&nbsp;{MESSAGE}</td>
                </tr>
                <tr>
                    <td class="w_padding" style='text-align:right;'><span class='question'>Username :</span> &nbsp;</td>
                    <td class="w_padding"> {USERNAME}</td>
                </tr>
                <tr>
                    <td class="w_padding" style='text-align:right;'><span class='question'>Password :</span> &nbsp;</td>
                    <td class="w_padding"> {PASSWORD}</td>
                </tr>
                <tr><td class="w_padding" colspan='2' height='4'></td></tr>
                <tr>
                    <td></td>
                    <td class="w_padding">
                        <input type='submit' class='some_btn' value='Send' />
                    </td>
                </tr>
                <tr>
                    <td colspan='2' class="w_padding" height='10'></td>
                </tr>
            </table>
            <input type='hidden' name='ocd' value='update' />
            </form>              

</fieldset>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
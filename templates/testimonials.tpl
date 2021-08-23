<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<fieldset>
    
<legend><div class="corner"></div>{MAIN_HEADER}</legend>

                <!-- BEGIN: TESTIMONIALS_ROW -->
<table width="100%" cellpadding="0" cellspacing="0" border='0' align='center'>
    <tr>
        <td>
            <span class='question'>{ROW_AUTHOR} ({ROW_LOCATION}) :</span>
        </td>
    </tr>
    <tr>
        <td style='padding:5px;'>
            <table width="100%" cellpadding="0" cellspacing="0" border='0' align='center'>
                <tr>
                    <td valign='top'>
                        {ROW_PHOTO}
                    </td>
                    <td width='100%' valign='top'>
                        <p class="text"><i style='color:#bfcad9;'>{ROW_DESCRIPTION}</i></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
    <!-- END: TESTIMONIALS_ROW -->

    <!-- BEGIN: TESTIMONIALS_EMPTY -->
<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr>
        <td class='w_border' align='center'>No testimonials here...</td>
    </tr>
</table>
    <!-- END: TESTIMONIALS_EMPTY -->   

</fieldset>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
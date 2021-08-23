<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

             <div style='text-align:center;'>{MESS}</div>
             
             <FIELDSET class="block">
            <LEGEND >&nbsp;{DICT.TF_Text1}&nbsp;</LEGEND>
            <table width="90%" cellpadding="2" cellspacing="0" border='0'>
                <tr>
                    <td class="w_padding">
                        <span class='question'> {DICT.TF_Subject} :</span>
                    </td>
                </tr>
                <tr>
                    <td class="w_padding">
                        {SUBJECT}
                    </td>
                </tr>
                <tr>
                    <td class="w_padding">
                        <span class='question'> {DICT.TF_Message} :</span>
                    </td>
                </tr>
                <tr>
                    <td class="w_padding">
                        {MESSAGE}
                    </td>
                </tr>
                
            </table>
            </fieldset>
            
<FIELDSET class="block">
<LEGEND >&nbsp;{DICT.TF_Sendingform}&nbsp;</LEGEND>
<form action='' method='POST' name='form1' style="padding:0px;margin:0px;">
    <table cellpadding="2" cellspacing="0" border="0" align="center">
        <tr>
            <td align="center" class="w_padding" colspan='4'>
                <span class='question' style="color:#ed8507;font-size:14px;">{DICT.TF_Sendto}:</span>
            </td>
        </tr>
        <tr>
            <td align="center" class="w_padding">
                &nbsp;
            </td>
            <td align="center" class="w_padding">
                <span class='question'>{DICT.TF_Firstname}:</span>
            </td>
            <td align="center" class="w_padding">
                <span class='question'>{DICT.TF_Lastname}:</span>
            </td>
            <td align="center" class="w_padding">
                <span class='question'>{DICT.TF_Email}:</span>
            </td>
        </tr>
        <!-- BEGIN: FORM_ROW -->
        <tr bgcolor='{FORM_BGCOLOR}'>
            <td align="center" class="w_padding">
                {COL_NUMBER}
            </td>
            <td align="center" class="w_padding">
                {COL_FIRST_NAME}
            </td>
            <td align="center" class="w_padding">
                {COL_LAST_NAME}
            </td>
            <td align="center" class="w_padding">
                {COL_EMAIL}
            </td>
        </tr>
        <!-- END: FORM_ROW -->
        <tr height='10'>
            <td colspan='4' class="w_padding"></td>
        </tr>
        <tr>
            <td align="center" colspan='4' class="w_padding">
            <button type="submit" class="btn btn-form" onClick="return confirm ('{DICT.TF_confirm}');" ><i class="fa fa-check"></i> {DICT.TF_Send}</button>
            </td>
        </tr>
        <tr height='10'>
            <td colspan='4' class="w_padding"></td>
        </tr>
    </table>
    <input type='hidden' name='ocd' value='send'>
</form>                    
            </fieldset>              

</fieldset>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
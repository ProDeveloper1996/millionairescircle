<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-comments fa-stack-1x"></i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row">

            <form action='{MAIN_ACTION}' method='POST'>
            <table width='90%' border='0' cellspacing='0' cellpadding='2' align='center'>
                <tr><td colspan='2' height='10'>{MAIN_CONFIRM}</td></tr>
                <tr>
<!--                    <td width='16%' nowrap class="w_padding" style='text-align:right;'><span class="question">Your e-mail address:</span> </td>-->
                    <td width='84%' class="w_padding">{MAIN_EMAIL} &nbsp; <span class='error'>{MAIN_EMAIL_ERROR}</span> </td>
                </tr>
                <tr>
<!--                    <td class="w_padding" style='text-align:right;'><span class="question">Ticket code :</span> </td>-->
                    <td class="w_padding">{MAIN_CODE} &nbsp; <span class='error'>{MAIN_CODE_ERROR}</span> </td>
                </tr>
                <tr><td colspan='2' height='5'></td></tr>
                <tr>
<!--                    <td>&nbsp;</td>-->
                    <td class="w_padding">
                    <button type="submit" class="btn btn-form-login" style="width:120px">Check</button>
                    </td>
                </tr>
                <tr><td colspan='2' height='5'></td></tr>
            </table>
            <input type='hidden' name='ocd' value='{MAIN_OCD}'/>
            </form>    


        </div>
    </div>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
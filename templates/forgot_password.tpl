<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-stack-1x">?</i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row">

            <form name='LoginForm' action='{PAGE_ACTION}' method='POST'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>

                <tr><td colspan='2' height='10'></td></tr>
                <tr>
                    <td colspan='2'>&nbsp;{MAIN_MESSAGE}</td>
                </tr>
                <tr><td colspan='2' height='10'></td></tr>
                <tr>
                    <td style='text-align:right;'><span class='question'>{DICT.FP_EmailAddress} :</span> &nbsp;</td>
                    <td> {LOGIN_EMAIL}</td>
                </tr>
                <tr><td colspan='2' height='10'></td></tr>

                <tr>
                    <td>
                    </td>
                    <td>
                        <button style="width:120px" type="submit" class="btn btn-form-login">{DICT.FP_Send}</button>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' height='20'></td>
                </tr>
            </table>
            <input type='hidden' name='ocd' value='remind'>
            </form>     

        </div>
    </div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
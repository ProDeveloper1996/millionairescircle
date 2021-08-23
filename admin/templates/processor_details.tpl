<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}</td>
    </tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>


        <form action='{MAIN_ACTION}' method='POST'>
        <table width='100%' border='0' cellspacing='0' cellpadding='2'>
            <tr>
                <td width='25%'>Processor name: <span title="The name of this payment processor." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_NAME} &nbsp; <span class='error'>{MAIN_NAME_ERROR}</span> </td>
            </tr>
            <tr>
                <td>Keyname: <span title="The keyname of this payment processor." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_CODE} &nbsp;</td>
            </tr>
            <tr>
                <td>Account ID: <span title="Your account ID in this processor." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_ACCOUNT_ID} &nbsp;<span class='error'>{MAIN_ACCOUNT_ID_ERROR}</span> </td>
            </tr>
            <tr>
                <td> <span class='signs_b'>Routine URL:</span> <span title="URL to the routines of this processor." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_ROUTINEURL} &nbsp;<span class='error'>{MAIN_ROUTINEURL_ERROR}</span> </td>
            </tr>
            <tr>
                <td> <span class='signs_b'>Fee:</span> <span title="The fee which will be added to each members payment via this processor." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_FEE} % &nbsp;<span class='error'>{MAIN_FEE_ERROR}</span> </td>
            </tr>

            <!-- BEGIN: MAIN_ALERTPAY -->
            <tr>
                <td>Security code: <span title="Security code for Payza processor. It will be used for verification of your payments." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_SECURECODE} &nbsp;<span class='error'>{MAIN_SECURECODE_ERROR}</span> </td>
            </tr>
            <!-- END: MAIN_ALERTPAY -->
            
            <!-- BEGIN: MAIN_LR_STORE -->
            <tr>
                <td>Liberty Reserve Merchant Store: <span title="The title of your Liberty Reserve Merchant Store. Text field, up to 50 characters in length." class="vtip"><img src='./images/question.png'></span></td>
                <td> {LR_STORE} &nbsp; </td>
            </tr>
            <!-- END: MAIN_LR_STORE -->
            
            <!-- BEGIN: MAIN_LR_SECUREWORD -->
            <tr>
                <td> <span class='signs_b'>Liberty Reserve Merchant Store Security Word:</span> <span title="Liberty Reserve Merchant Store Security Word." class="vtip"><img src='./images/question.png'></span></td>
                <td> {LR_SECUREWORD} &nbsp; </td>
            </tr>
            <tr><td colspan='3' height='10'></td></tr>
            <tr>
                <td colspan='3'>
                    Note: There is no restriction on length of Security Word. It is encoded in database and if the word is
                    there then system just shows it as 8 dots. Don't change these dots if you didn't change the Security Word.
                </td>
            </tr>
            <!-- END: MAIN_LR_SECUREWORD -->

            <tr><td colspan='3' height='10'><hr></td></tr>
<!--
            <tr>
                <td> <span class='signs_b'>Alternative password:</span> <span title="To change these settings you have to enter Alternative Admin Password. This passowrd can be changed on Admin Settings page." class="vtip"><img src='./images/question.png'></span></td>
                <td> {MAIN_PASSWORD} &nbsp;<span class='error'>{MAIN_PASSWORD_ERROR}</span> </td>
            </tr>
-->
            <tr>
               <td colspan='2'>
                   <input type='submit' class='some_btn' value=" Apply "> &nbsp; <input type='button' class='some_btn' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>        

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
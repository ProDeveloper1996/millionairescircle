<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='message'>{MAIN_MESSAGE}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>

<form action='{ACTION_SCRIPT}' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
<!--
    <tr>
        <td width='30%'><span class='signs_b'>Admin Username:</span></td>
        <td width='20'>
            <span title="Administrator Username for loging in." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_ADMIN_USERNAME} &nbsp; <span class='error'>{MAIN_ADMIN_USERNAME_ERROR}</span> </td>
    </tr>
-->
    <tr>
        <td><span class='signs_b'>Contact e-mail address:</span></td>
        <td>
            <span title="Administrator email. It is used for all contact requests." class="vtip"><img src='./images/question.png'></span> 
        </td>
        <td> {MAIN_CONTACTEMAIL} &nbsp; <span class='error'>{MAIN_CONTACTEMAIL_ERROR}</span> </td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr>
        <td><span class='signs_b'>Enable SMTP Authorization:</span></td>
        <td>
            <span title="Tick this option if your email SMTP server requires autorization to send emails. All fields should not be blank." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_USESMTPAUTORISATION} &nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;<span class='signs_b'>SMTP Server:</span></td>
        <td>

        </td>
        <td> {MAIN_SMTPSERVER} &nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;<span class='signs_b'>SMTP Domain:</span></td>
        <td>

        </td>
        <td> {MAIN_SMTPDOMAIN} &nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;<span class='signs_b'>SMTP Username:</span></td>
        <td>

        </td>
        <td> {MAIN_SMTPUSERNAME} &nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;<span class='signs_b'>SMTP Password:</span></td>
        <td>

        </td>
        <td> {MAIN_SMTPPASSWORD} &nbsp;</td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <!--
    <tr>
        <td><span class='signs_b'>New Admin Password:</span></td>
        <td>
            <span title="New Administrator password. Leave blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_ADMIN_PASSWORD} &nbsp; <span class='error'>{MAIN_ADMIN_PASSWORD_ERROR}</span> </td>
    </tr>
    <tr>
        <td><span class='signs_b'>Confirm Admin Password:</span></td>
        <td>
            <span title="Confirm New Administrator password. Leave blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_ADMIN_PASSWORD1} &nbsp; <span class='error'>{MAIN_ADMIN_PASSWORD1_ERROR}</span> </td>
    </tr>
    <tr>
        <td><span class='signs_b'>Current Admin Password:</span></td>
        <td>
            <span title="Enter current Administrator password. Required to confirm your changes." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_CURRENT_PASSWORD} &nbsp; <span class='error'>{MAIN_CURRENT_PASSWORD_ERROR}</span> </td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr>
        <td><span class='signs_b'>New Alternative Admin Password:</span></td>
        <td>
            <span title="New Administrator alternative password (Used for changes in the Payment Processors you use for payments). Leave this field blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_ADMIN_ALT_PASSWORD} &nbsp; <span class='error'>{MAIN_ADMIN_ALT_PASSWORD_ERROR}</span> </td>
    </tr>

    <tr>
        <td><span class='signs_b'>Confirm New Alternative Admin Password:</span></td>
        <td>
            <span title="Confirm New Administrator alternative password. Leave blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_ADMIN_ALT_PASSWORD1} &nbsp; <span class='error'>{MAIN_ADMIN_ALT_PASSWORD1_ERROR}</span> </td>
    </tr>

    <tr>
        <td><span class='signs_b'>Current Alternative Admin Password:</span></td>
        <td>
            <span title="Current Administrator's alternative password (Same as the main one by default). Required to confirm the change of Administrator's alternative password." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_CURRENT_ALT_PASSWORD} &nbsp; <span class='error'>{MAIN_CURRENT_ALT_PASSWORD_ERROR}</span> </td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr>
        <td><span class='signs_b'>Enable security mode:</span></td>
        <td>
            <span title="Tick this option to enable security mode. The system saves current IP address and sends notification if the attempt to login to Admin Panel from another IP was made." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_SECURITYMODE} &nbsp;&nbsp;&nbsp;&nbsp; <a class='menu' href='{ACTION_SCRIPT}?ocd=downlog'><img src='./images/down.png' border='0' title='Download log of admin logins' /></a> </td>
    </tr>
-->

    <tr>
        <td><span class='signs_b'>Current Admin Password:</span></td>
        <td>
            <span title="Enter current Administrator password. Required to confirm your changes." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_CURRENT_PASSWORD} &nbsp; <span class='error'>{MAIN_CURRENT_PASSWORD_ERROR}</span> </td>
    </tr>

</table>
        </td>
    </tr>
    <tr>
        <td align='center'>
            <input class='some_btn' type='submit' value="Update">
            <input type='hidden' name='ocd' value='update'>
        </td>
    </tr>
        
        </form>
    </tr>
</table>



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td height='20'>{MAIN_HEADER}</td></tr>

</table>

<form action='{MAIN_ACTION}' method='POST' enctype='multipart/form-data'>

<table width='100%' border='0' cellspacing='0' cellpadding='3'>

    <tr>
    <td><hr><H4>Main Settings</H4></td>
    </tr>
    <tr>
        <td>

<table border='0' cellspacing='0' cellpadding='2' align='left' width='100%'>
    <tr>
        <td>Username:</td>
        <td> {MAIN_USERNAME} &nbsp; <span class='error'>{MAIN_USERNAME_ERROR}</span></td>
    </tr>

    <tr>
        <td>E-mail address:</td>
        <td> {MAIN_EMAIL} &nbsp; <span class='error'>{MAIN_EMAIL_ERROR}</span> </td>
    </tr>

    <tr>
        <td><span class='signs_b'>New Admin Password:</span><span title="New Administrator password. Leave blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span></td>
        <td> <input type='password' name='AdminPassword' value='' maxlength='12' style='width:160px;'> &nbsp; <span class='error'>{MAIN_ADMIN_PASSWORD_ERROR}</span> </td>
    </tr>
    <tr>
        <td><span class='signs_b'>Confirm Admin Password:</span><span title="Confirm New Administrator password. Leave blank if you don't want to change it." class="vtip"><img src='./images/question.png'></span></td>
        <td> <input type='password' name='AdminPassword1' value='' maxlength='12' style='width:160px;'> &nbsp; <span class='error'>{MAIN_ADMIN_PASSWORD1_ERROR}</span> </td>
    </tr>
<!--
    <tr>
        <td><span class='signs_b'>Current Admin Password:</span></td>
        <td>
            <span title="Enter current Administrator password. Required to confirm your changes." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> <input type='text' name='CurrentPassword' value='' maxlength='12' style='width:160px;'> &nbsp; <span class='error'>{MAIN_CURRENT_PASSWORD_ERROR}</span> </td>
    </tr>
-->
</table>

     </td>
    </tr>
    <tr>
        <td><hr><H4>Access</H4></td>
    </tr>
    <tr>
        <td>

<table border='0' cellspacing='0' cellpadding='2' align='left' width='100%'>
    <tr>
        <td width='20%'></td>
        <td> {MAIN_ACCESS}</td>
    </tr>
</table>

     </td>
    </tr>


    <tr>
        <td>
<table border='0' cellspacing='0' cellpadding='2' align='center' width='100%'>
    <tr>
        <td colspan='2'>
            <input class='some_btn' type='submit' value=" Update "> &nbsp;
            <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">
        </td>
    </tr>
</table>
<input type='hidden' name='ocd' value='{MAIN_OCD}'>
<input type='hidden' name='id' value='{MAIN_ID}'>
        </td>
    </tr>
</table>

</form>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
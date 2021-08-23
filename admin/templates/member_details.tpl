<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td height='20'>{MAIN_HEADER}</td></tr>

</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3'>

    <tr>
        <td>
            <H4>Main Information</H4>
        </td>
    </tr>
    <tr>
        <td>

<table border='0' cellspacing='0' cellpadding='2' width='100%'>
    <tr>
        <td width='20%'>Registration Date:</td>
        <td> {MAIN_REG}</td>
    </tr>
    <tr>
        <td> Last Access:</td>
        <td> {MAIN_LAST}</td>
    </tr>
    <tr>
        <td>Sponsored members:</td>
        <td> {MAIN_SPONSORED}</td>
    </tr>
    <tr>
        <td>Earnings:</td>
        <td>{CURRENCY}{MAIN_EARNED}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
    <td><hr><H4>Main Settings</H4></td>
    </tr>
    <tr>
        <td>

<form action='{MAIN_ACTION}' method='POST' enctype='multipart/form-data'>
<table border='0' cellspacing='0' cellpadding='2' align='left' width='100%'>
    <tr>
        <td width='20%'>First name:</td>
        <td> {MAIN_FIRST_NAME} &nbsp; <span class='error'>{MAIN_FIRST_NAME_ERROR}</span></td>
    </tr>

    <tr>
        <td>Last name:</td>
        <td> {MAIN_LAST_NAME} &nbsp; <span class='error'>{MAIN_LAST_NAME_ERROR}</span></td>
    </tr>

    <tr>
        <td>Email:</td>
        <td> {MAIN_EMAIL} &nbsp; <span class='error'>{MAIN_EMAIL_ERROR}</span></td>
    </tr>
    <tr>
        <td>Sponsor's ID:</td>
        <td> {MAIN_SPONSOR} &nbsp; <span class='error'>{MAIN_SPONSOR_ERROR}</span></td>
    </tr>
    <tr>
        <td>Member Level:</td>
        <td> {MAIN_LEVEL} &nbsp; <span class='error'>{MAIN_LEVEL_ERROR}</span></td>
    </tr>
    <tr>
        <td>Username:</td>
        <td> {MAIN_USERNAME} &nbsp; <span class='error'>{MAIN_USERNAME_ERROR}</span></td>
    </tr>
    <tr>
        <td>Password:</td>
        <td> {MAIN_PASSWD}</td>
    </tr>
    <tr>
        <td colspan='2'> {MAIN_IP} </td>
    </tr>
</table>

     </td>
    </tr>
    <tr>
        <td><hr><H4>Address Settings</H4></td>
    </tr>
    <tr>
        <td>

<table border='0' cellspacing='0' cellpadding='2' align='left' width='100%'>
    <tr>
        <td width='20%'>Address:</td>
        <td> {MAIN_ADDRESS}</td>
    </tr>

    <tr>
        <td>City:</td>
        <td> {MAIN_CITY}</td>
    </tr>

    <tr>
        <td>State:</td>
        <td> {MAIN_STATE}</td>
    </tr>
    <tr>
        <td>Country :</td>
        <td> {MAIN_COUNTRY}</td>
    </tr>
    <tr>
        <td>Postal Code :</td>
        <td> {MAIN_POSTAL}</td>
    </tr>
    <tr>
        <td>Phone :</td>
        <td> {MAIN_PHONE}</td>
    </tr>
</table>

     </td>
    </tr>
    <tr>
        <td><hr>
            <H4>Payment settings</H4>
        </td>
    </tr>
    <tr>
        <td>

<table cellpadding="2" cellspacing="0" border='0' width='100%'>
    <tr>
        <td width='20%'>Payment Processor:</td>
        <td>{MAIN_PROCESSOR}</td>
    </tr>
    <tr><td>Account ID:</td>
        <td>{MAIN_ACCOUNT_ID}</td>
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
</form>
        </td>
    </tr>
</table>



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
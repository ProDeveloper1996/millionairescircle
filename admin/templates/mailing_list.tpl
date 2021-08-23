<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr><td class='message'>{MAIN_CONFIRM}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
    <tr><td height='12' colspan='2'><h3>List of members to send Mass Mail</h3></td></tr></table>
<form action={MAIN_ACTION}?pg=0 method='POST' enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_MEMBER_ID}</td>
        <td>{HEAD_USERNAME}</td>
        <td>{HEAD_FIRST_NAME}</td>
        <td>{HEAD_LAST_NAME}</td>
        <td>{HEAD_EMAIL}</td>
        <td>{HEAD_SPONSOR}</td>
        <td>{HEAD_REG_DATE}</td>
        <td>{HEAD_CHECKBOX}</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_MEMBER_ID}&nbsp;</td>
        <td>{ROW_USERNAME}</td>
        <td>{ROW_FIRST_NAME}&nbsp;</td>
        <td>{ROW_LAST_NAME}</td>
        <td>{ROW_EMAIL}&nbsp;</td>
        <td>{ROW_SPONSOR}</td>
        <td>{ROW_REG_DATE}</td>
        <td>{ROW_CHECKBOX}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='w_border' colspan='8' align='center'>The list of members to send Mass Mail is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>
<br>
<table width='100%' border='0' cellspacing='0' cellpadding='10'>
    <tr>
        <td align='left'>
            <input class='some_btn' type='submit' value='Remove all from selected list' onClick="this.form.ocd.value='clear'; return true;">
        </td> 
        <td align='right'>
            <input class='some_btn' type='submit' value='Remove checked from selected list'>
            <input type='hidden' name='ocd' value='delsome'>
        </td>
    </tr>
</table>
</form>
<form action='mailing.php?ocd=mail' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
    <tr><td colspan='2'><span class='message'><b>{LIST_EMPTY}</b></span></td></tr>
    <tr><td align='center'><span class='signs_b'><h4>Proceed Mass Mailing by clicking on the button below.</h4></td></span>
         </br>
        <tr><td align='left'>{SEND_EMAIL}</td> </tr>
    </tr>
</table>
<input type='hidden' name='ocd' value='email'>
</form>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='12'></td></tr>
    <tr><td align='right'> {MAIN_PAGES} &nbsp; </td></tr>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<form action={MAIN_ACTION}?pg=0 method='POST'>
<table border='0' cellspacing='0' cellpadding='5' class='filter'>
    <tr>
        <td width='80%'>
            <table width='100%' border='0' cellspacing='0' cellpadding='2'>
                <tr>
                    <td>
                        Member ID {MEMBER_ID}&nbsp; Sponsor ID {SPONSOR_ID}<br>
                    </td>
                </tr>
                <tr>
                    <td>
                        Last name {LAST_NAME}&nbsp; E-mail {EMAIL}<br>
                    </td>
                </tr>
                <tr>
                    <td>
                        {MAIN_FILTER}
                    </td>
                </tr>
            </table>
        </td>
        <td width='10%'><input type='submit' class='some_btn' value=" Apply "></td>
    </tr>
    <input type='hidden' name='filter' value=1>
</table>
</form>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr><td><h3>Select required members and press "Move to selected list" button below.</h3></td></tr>
</table>
<form action={MAIN_ACTION}?pg=0 method='POST' enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' class="simple-little-table">
    <tr>
        <td>{HEAD_MEMBER_ID}</td>
        <td>{HEAD_FIRST_NAME}</td>
        <td>{HEAD_LAST_NAME}</td>
        <td>{HEAD_USERNAME}</td>
        <td>{HEAD_EMAIL}</td>
        <td>{HEAD_SPONSOR}</td>
        <td>{HEAD_REG_DATE}</td>
        <td width='25'>{HEAD_CHECKBOX}</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td>{ROW_MEMBER_ID}&nbsp;</td>
        <td>{ROW_FIRST_NAME}</td>
        <td>{ROW_LAST_NAME}</td>
        <td>{ROW_USERNAME}</td>
        <td>{ROW_EMAIL}</td>
        <td>{ROW_SPONSOR}</td>
        <td>{ROW_REG_DATE}</td>
        <td width='20'>{ROW_CHECKBOX}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='8' align='center'>The list of members is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='12'></td></tr>
    <tr><td align='right'> {MAIN_PAGES} &nbsp; </td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='5'>
    <tr>
        <td align='left' width='50%'>You can add particular groups of your members to the list by clicking on buttons below:</br>
            <input class='some_btn' type='submit' value='Add ALL members' onClick="this.form.ocd.value='moveall'; return true;">
            &nbsp;
            <input class='some_btn' type='submit' value='Add INACTIVATED members' onClick="this.form.ocd.value='moveinact'; return true;">
            &nbsp;
            <input class='some_btn' type='submit' value='Add FREE members' onClick="this.form.ocd.value='movefree'; return true;">
            &nbsp;
            <input class='some_btn' type='submit' value='Add PAID members' onClick="this.form.ocd.value='movepaid'; return true;">

        </td>

        <td align='right' valign='top'>Check the boxes beside the members you want to send email to and click on the button.</span></br>
            <input class='some_btn' type='submit' value='Move to selected list'>
            <input type='hidden' name='ocd' value='moveto'></br>
            
        </td>
    </tr>
    <tr><td height='12' colspan='2'><hr></td></tr>
    <tr><td><span class='signs_b'>Proceed (or clear the list) mass mailing by clicking on the icon below. Currently {AMOUNT_SEL} members are added to the list.</span>
         </br><a href='mailing.php?ocd=sellist&pg=0'><img src='./images/toselect.png' title='Go to the selected list' border='0'></a></td></tr>
</table>
</form>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td height='20' colspan='2'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='10' colspan='2'></td></tr>
    <tr><td><span class='message'>{MAIN_MESSAGE}</span></td><td align='right'> {MAIN_ADDLINK} &nbsp; </td></tr>
    <tr><td height='12' colspan='2'></td></tr>
</table>


<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>&nbsp;&nbsp;{HEAD_TITLE}</td>
        <td align='center' width='60' colspan='3'><b>Action</b></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td>{ROW_TITLE}</td>
        <td align='center' width='20'>{ROW_DOWNLINK}</td>
        <td align='center' width='20'>{ROW_RECOVERLINK}</td>
        <td align='center' width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td colspan='4' align='center'>The list of backups is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table> 
<br />
<br />
<table width='100%' border='0' cellspacing='0' cellpadding='2' bgcolor='#F5F5F5'>
    <tr>
        <td width='40%' align='center'  height='100px'>
            <span style='color:red;'>Using this button you can reset your Database to initial settings. Be careful! All the data will be destroyed!</span><br /><br />
            <form action='{MAIN_ACTION}' method='POST' name='form1'>
                <input type='submit' value=" Reset Your Database " style='background-color:#FF4500;color:#ffffff;' onClick="return confirm ('Are you sure you want to reset your DB?<br />It will delete all your previous settings!');">
                <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            </form>
        </td>
        <td align='center'>
            <span style='color:red;'>Use these functions for Test purposes only. Clicking on 'Pay for All' button will pay for all the unpaid members in the system and place them into the matrices. Clicking on 'Clear All' button will change the status of all paid members to unpaid and will clear all the matrices and commissions already paid.</span><br /><br />
            <a onClick="return confirm ('Are you sure you want to make payment for ALL your members?');" class="some_btn" href='members.php?ocd=pay_all'>Pay for All</a>
<a onClick="return confirm ('Are you sure you want to remove ALL the members from the system?');" class="some_btn" href='members.php?ocd=reset_all'>Clear All</a>
 </td>
    </tr>
</table>

<br>
<a href="/admin/upload_members.php">Upload Members from file</a>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
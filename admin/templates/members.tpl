<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td height='20'>{MAIN_HEADER}</td>
        <td align='right'><div class="spoiler_style" onClick="open_close('spoiler1')">
         HELP GUIDE
</div></td>
    </tr>
</table>
<table width='100%'><tr><td><div id="spoiler1" style="display:none;">
<b>ID</b> - is the ID of the member in the system <br>
<b>Member Details</b> - Clicking on the icon opens the page with the full information about the member and ability for admin to edit it <br>
<b>Username</b> - The Usernames of the members. Clicking on Username link opens the backoffice of the member in new tab<br>
<b>Email</b> - The members email used on registration. Clicking on Email link allow to send email directly to the member's email address<br>
<b>Referrer ID</b> - The ID of the member who invited the member<br>
<b>Level</b> - Members current level<br>
<b>Earnings</b> - Members current amount in cash account. Admin can add or substract commission manually by clicking on <img width="15px" src="images/plus_minus.gif"> icon<br>
<b>Replicas</b> - Click on the  <img width="15px" src="images/replica_icon.png"> icon to be able to view and administrate the created by member replicated site. Click on the <img width="15px" src="images/replica0.png"> icon to activate the <a target='blank' href='http://runmlm.com/content.php?p_id=10'>Replicated site</a> created by members. Click on <img width="15px" src="images/replica1.png"> icon to deactivates the <a target='blank' href='http://runmlm.com/content.php?p_id=10'>Replicated site</a> created by members<br> 
<b>Security</b> - Empty if you did not activate the 'Use Secure Mode for members accounts' function on Site Settings page or member does not use the Secure mode for log in. Icon appears when member activates Secure Mode on their Account details page. Clicking on the <img width="15px" src="images/key_icon.png"> icon will automatically generate new password and email it to member<br>
<b>Paid</b> - This field is empty (if you use <a target='blank' href='http://runmlm.com/content.php?p_id=16'>Cycling Mode</a>) or you see <img width="15px" src="images/money_no.png"> (if you use <a target='blank' href='http://runmlm.com/content.php?p_id=17'>Forced Mode</a>) if member has paid for membership and is placed to the structure. If you see <img width="15px" src="images/money.png"> icon it means that member did not pay yet or paid using some way that does not place them automatically to the system. Clicking on the <img width="15px" src="images/money.png"> icon will pay for member and place them to the matrix according to your matrix pattern<br>
<b>Matrices</b> - Clicking on <img width="15px" src="images/viewmatrix.png"> icon will open the page with all the matrices member is in<br>
<b>Actions</b><br> 
<b>Members activity status.</b><br> 
<img width="15px" src="images/active0.png"> is shown when member is inactive and access to backoffice is denied. <br>
<img width="15px" src="images/active1.png"> means that member is active and can log in to the system. You can always change the status manually.<br><br>
<b>Members deletion status.</b><br> 
<img width="15px" src="images/dead0.png"> means that member is active and can log in to the system. You can always change the deletion status manually. As soon as you change it to Deleted and see <img width="15px" src="images/dead1.png"> icon, the member is not more a part of the system, members direct referrals will automatically be member #1 (admin) direct referrals. Places in all the matrices for such a member are frosen and you see <img height="15px" src="images/red_man.png"> instead of members details. Access to backoffice for such amember is denied<br><br>
<b>Completely remove member</b><br>
Clicking on the trash icon deletes all the member's data and removes the member from all the matrices.<br>
<b>NOTE:</b> It is strongly recommended you use this function only in high rate emergency. Use Members deletion status to deactivate members!<br><br>
<a target='blank' href='/backup.php'>'Pay for all' and 'Reset All'</a> functions will allow you to make tests or pay for all the members at one time. Also you can make a copy of the current DB state there. 
</div>
</td>
    </tr>
</table>    


<table width='100%' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td>
<form action={MAIN_ACTION}?pg=0 method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' class='filter'>
    <tr><td>Filter</td></tr>
    <tr>

                    <td>{SEARCH_LIST} </td>
                    <td>{SEARCH_LINE} </td>
                    <td>{MAIN_FILTER} </td>
                    <td><input type='submit' class='some_btn' value=" Apply "><input type='hidden' name='filter' value=1></td>
    </tr>
    </table>
</form> 
</td>
</tr>
<tr> 
<td style='align:right;'>{MAIN_ADDLINK}</td>
</tr>
</table>   

<table width='100%' border='0' cellspacing='0' cellpadding='0' class="simple-little-table">
    <tr>
        <td>{HEAD_MEMBER_ID}</td>
        <td style='color:#2EA6E6'><b>Details</b></td>        
        <td >{HEAD_USERNAME}</td>
        <td>{HEAD_FIRST_NAME}</td>
        <td>{HEAD_LAST_NAME}</td>
        <td>{HEAD_EMAIL}</td>
        <td>{HEAD_SPONSOR}</td>
        <td>{HEAD_LEVEL}</td>
        <td>{HEAD_EARNINGS}</td>
<!--        <td>{HEAD_REG_DATE}</td> -->
        <td colspan='2' style='color:#2EA6E6'><b>Replicas</b></td>
        <td colspan='2' style='color:#2EA6E6'><b>Security</b></td>
        <td style='color:#2EA6E6'><b>Paid</b></td>
        <td style='color:#2EA6E6'><b>Matrices</b></td>
        <td colspan='3' style='color:#2EA6E6'><b>Activity</b></td>
</tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_MEMBER_ID}&nbsp;</td>
        <td>{ROW_EDITLINK}</td>        
        <td>{ROW_USERNAME}</td>
        <td>{ROW_FIRST_NAME}&nbsp;</td>
        <td>{ROW_LAST_NAME}&nbsp;</td>
        <td>{ROW_EMAIL}&nbsp;</td>
        <td>{ROW_SPONSOR}</td>
        <td>{ROW_LEVEL}</td>
        <td>{ROW_INOUTCASH}&nbsp;{CURRENCY}{ROW_EARNINGS}</td>
<!--        <td>{ROW_REG_DATE}</td>-->
        <td>{ROW_REPLICALINK}</td>
        <td>{ROW_ISREPLICALINK}</td>
        <td>{ROW_IPLINK}</td>
        <td>{ROW_PASSLINK}</td>
        <td>{ROW_PAYLINK}</td>
        <td>{ROW_MATRIX}</td>
        <td>{ROW_ACTIVELINK}</td>
        <td>{ROW_DELLINK}</td>
        <td>{ROW_DELFOREVER}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td colspan='20' align='center'>The list of members is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

{MAIN_PAGES}



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
            
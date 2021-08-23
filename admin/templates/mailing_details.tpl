<!-- BEGIN: MAIN -->

{FILE{HEADER_TEMPLATE}}

<table class="topic" border="0" cellpadding="0" cellspacing="0" align='center' width='100%'>
    <tr><td align='left'><H4>{MAIN_HEADER}</H4><hr></td></tr>
    <tr><td align = "left"><h4>This email will be sent to "{TOTAL_LIST}" member(s)</h4><td>
    </tr>
    <tr><td height='12'></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>
        
        
    <table cellpadding="2" cellspacing="0" border="0" align="center" width='100%'>
        <tr>
            <td>Select template:</td>
            <td>
            <form action='' method='POST'>
                {MAIN_SELECT}
            </form>
            </td>
        </tr>
        <tr>
            <td>
                <form action='mailing.php?ocd=send_email' method='POST' name='form1'>
                Email Subject:
            </td>
            <td>
                {EMAIL_SUBJECT}
            </td>
        </tr>
        <tr>
            <td valign='top'>Email Message:</td>
            <td>
                {EMAIL_MESSAGE}
            </td>
            <td>Substitutions:</br>
                {CHANGE_TEMPLATE}
            </td>
        </tr>
        <tr>
            <td valign='top'>
            </td>
            <td valign='top' align='left'>
                {MAIN_FLAG}
            </td>
        </tr>
 </table>
    

</td>
    </tr>
    <tr style='height:30px;'>
        <td class='w_border' align='center'>
            <input class='some_btn' type='submit' value="Send Mass Mail " onClick="return confirm ('Are you sure?');">
                <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='mailing.php?ocd=sellist'">

            <input type='hidden' name='ocd' value='send_email'>
            </form>
        </td>
    </tr>
</table>

{FILE{FOOTER_TEMPLATE}}

<!-- END: MAIN -->
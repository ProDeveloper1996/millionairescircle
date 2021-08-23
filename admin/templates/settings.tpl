<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='message'>{MAIN_CONFIRM}</td></tr>
</table>
<form action='{ACTION_SCRIPT}' method='POST'>


<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td width='400'><span class='signs_b'>Site Title:</span></td>
        <td width='20'>
            <span title="Site Title." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_SITE_TITLE} &nbsp; <span class='error'>{MAIN_SITE_TITLE_ERROR}</span> </td>
    </tr>
    <tr>
        <td width='400'><span class='signs_b'>Site URL:</span></td>
        <td width='20'>
            <span title="Site URL." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_SITE_URL} &nbsp; <span class='error'>{MAIN_SITE_URL_ERROR}</span> </td>
    </tr>
    <tr>
        <td width='400'><span class='signs_b'>Site folder path:</span></td>
        <td width='20'>
            <span title="Physical path to the root of this site on the server." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_PATH_SITE} &nbsp; <span class='error'>{MAIN_PATH_SITE_ERROR}</span> </td>
    </tr>
<!--
    <tr>
        <td width='400'><span class='signs_b'>Secure Mode for members:</span></td>
        <td width='20'>
            <span title="Enables/Disables IP protection for members accounts." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_SECURE}</td>
    </tr>
-->
    <tr>
        <td width='400'><span class='signs_b'>Use Autoresponder Function:</span></td>
        <td width='20'>
            <span title="Use Autoresponder Function. Please create and activate templates for Autoresponder in Emailing section." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_AUTO}</td>
    </tr>
    {MAIN_VALIDATION}
{NUMBER_TURING}
</table>
        </td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr><td><H4>Finances Settings</H4></td></tr>
    <tr>
        <td>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    {MAIN_CURRENCY}
    <tr>
        <td width='400'><span class='signs_b'>Currency rate:</span></td>
        <td width='20'>
        </td>
        <td> <input type="text" name="currency_rate" value="{MAIN_CURRENCY_RATE}" style="width:100px;"></td>
    </tr>

{MAIN_ESHOP}
    <tr>
        <td width='400'><span class='signs_b'>The title/name of your product:</span></td>
        <td width='20'>
            <span title="The product you are going to sell to your members." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {MAIN_PRODUCT} &nbsp; <span class='error'>{MAIN_PRODUCT_ERROR}</span> </td>
    </tr>
    <tr>
        <td width='400'><span class='signs_b'>Min sum of withdrawal:</span></td>
        <td width='20'>
            <span title="Only having this sum or more a member can make withdrawal request. Also the 'Withdrawal Request' button appears only if member has this or more sum." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {CURRENCY} {MAIN_CASH_OUT} &nbsp; <span class='error'>{MAIN_CASH_OUT_ERROR}</span> </td>
    </tr>
    <tr>
        <td width='400'><span class='signs_b'>Withdrawal Fee:</span></td>
        <td width='20'>
            <span title="Admin fee on every withdrawal request." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td> {_CURRENCY}{WITHDRAWAL_VALUE} {MAIN_FEE} &nbsp; <span class='error'>{MAIN_FEE_ERROR}</span> </td>
    </tr>
    {MAIN_PAYPFROMCASH}
    
{IS_PIF}
{IS_PIF_CASH}
    
    </table>
        </td>
    </tr>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr><td><H4>Matrix Settings</H4></td></tr> 
    <tr>
        <td>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Matrix Pattern:</span></td>
        <td width='20' valign='top'>
            <span title="Warning! A very important setting! Choose the type of your matrix. Don't forget to press 'Update' button below after you chose the type." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top' width='20%' style="line-height:20px;">
            {MAIN_TYPE}
        </td>
        <td><span title="<b><span color='red'>WARNING! MAKE THIS SETTING ONLY ONCE. OTHERWISE ALL THE DATA AND OTHER SETTINGS INCLUDING YOUR MEMBERS WILL BE DELETED.</span></b>" class="vtip"><img src='./images/alert.png'></span></td>
    </tr>

</table>

        </td>
    </tr>
        <tr>
        <td>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Sponsor Bonus:</span></td>
        <td width='20' valign='top'>
            <span title="Automatic payout for every certain amount of sponsored members." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {_CURRENCY}{SPONSOR_VALUE} {SPONSOR_AMOUNT} for every {SPONSOR_QUANT} sponsored members.
        </td>
    </tr>
</table>

</td>
    </tr>
    <tr>
        <td>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>For visitors without Referral Link:</span></td>
        <td width='20' valign='top'>
            <span title="Assigns a random active member as a sponsor for registration without Referral link in case first value is chosen.<br />Assigns Admin (member #1) as a sponsor for registration without Referral link in case second value is chosen." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top' style="line-height:20px;">
            {MAIN_RANDOM}
        </td>
    </tr>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Commissions Value :</span></td>
        <td width='20' valign='top'>
            <span title="Commissions Value" class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {COMMISSIONS_VALUE}
        </td>
    </tr>


    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr><td><H4>Members Promotional Tools</H4></td></tr>
{REPLICA}
{QUANT_REPLICA}

            {PTOOLS}

<!--
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Max number of the text adds members can create:</span></td>
        <td width='20' valign='top'>
            <span title="Each active member can create this number of text ads visible on this site." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {QUANT_TEXTADDS} &nbsp; <span class='error'>{QUANT_TEXTADDS_ERROR}</span>
        </td>
    </tr>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Number of the text adds shown at once on public area :</span></td>
        <td width='20' valign='top'>
            <span title="Number of the text ads shown at once on public area." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {QUANT_TEXTADDS_SHOW} &nbsp; <span class='error'>{QUANT_TEXTADDS_SHOW_ERROR}</span>
        </td>
    </tr>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Number of the text adds shown at once inside member area :</span></td>
        <td width='20' valign='top'>
            <span title="Number of the text ads shown at once inside member area." class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {QUANT_TEXTADDS_SHOW_M} &nbsp; <span class='error'>{QUANT_TEXTADDS_SHOW_M_ERROR}</span>
        </td>
    </tr>
-->
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Referral link viewing :</span></td>
        <td width='20' valign='top'>
            <span title="Referral link viewing" class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            {REFERRER_URL}
        </td>
    </tr>


    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr><td><H4>Matching Bonus</H4></td></tr>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Matching Bonus :</span></td>
        <td width='20' valign='top'>
            <span title="Matching Bonus" class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            <input type="checkbox" name="matching_bonus" {MATCHING_BONUS}>
        </td>
    </tr>
    <tr>
        <td width='400' valign='top'><span class='signs_b'>Matching Bonus Value:</span></td>
        <td width='20' valign='top'>
            <span title="Matching Bonus Value" class="vtip"><img src='./images/question.png'></span>
        </td>
        <td valign='top'>
            <input type="text" name="matching_bonus_value" value="{MATCHING_BONUS_VALUE}" maxlength="6" style="width:100px;"> %
        </td>
    </tr>

</table>

        </td>
    </tr>

    <tr>
            <td align='center'>
                <input class='some_btn' type='submit' value="Update" onClick="return confirm ('Confirm the changes');">
                <input type='hidden' name='ocd' value='update'>
            </td>
        
        </form>
    </tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
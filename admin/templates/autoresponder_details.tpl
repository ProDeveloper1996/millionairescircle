<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>
        
<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data' name='form1'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='20%' align='left' valign='top'> <span class='signs_b'>Subject:</span> </td>
        <td width='80%'> {MAIN_SUBJECT}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_SUBJECT_ERROR}</span></td></tr>
    <tr>
        <td width='20%' align='left' valign='top'> <span class='signs_b'>Days after upgrade:</span> </td>
        <td width='80%'> {MAIN_Z_DAY} <span class='hidden'>The message will be sent after the set amount of days.</span></td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_Z_DAY_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'> <span class='signs_b'>Message:</span> </td>
        <td> {MAIN_MESSAGE}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_MESSAGE_ERROR}</span></td></tr>
    
    <tr>
        <td align='left' valign='top'> <span class='signs_b'>Substitutions: <span title="Put the cursor to the place where you want to
        add substitutioins and then click on any from the list. This information will be placed to the email message" class="vtip">
        <img src='./images/question.png'></span></td>
        <td> 
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[SiteTitle]');">SiteTitle</a>] - Title of the site<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[SiteUrl]');">SiteUrl</a>] - URL of the site<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[MemberID]');">MemberID</a>] - Member's ID<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[MemberUsername]');">MemberUsername</a>] - Member's Username<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[FirstName]');">FirstName</a>] - Member's First Name<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[LastName]');">LastName</a>] - Member's Last Name<br />
            [<a href="javascript:void(0);" title="click to paste" onClick="insertText ('[SponsorID]');">SponsorID</a>] - ID of Member's Sponsor<br />
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class='some_btn' type='submit' value=" Update "> &nbsp;
            <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
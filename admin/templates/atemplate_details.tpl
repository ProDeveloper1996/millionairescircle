<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4><hr></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>
        
        
<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data' name='form1'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='15%'>Subject:</td>
        <td> {MAIN_SUBJECT}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_SUBJECT_ERROR}</span></td></tr>
    <tr>
        <td valign='top'>Message:</td>
        <td> {MAIN_MESSAGE}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_MESSAGE_ERROR}</span></td></tr>
    <tr>
        <td valign='top'>Substitutions:
        <span title="Put the cursor to the place where you want to add substitutioins and then click on any from the list. This information will be placed to the email message" class="vtip"><img src='./images/question.png'></span></td>
        <td> {SUBSTITUTIONS}</td>
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
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
        <td width='20%' align='left' valign='top'> <span class='signs_b'>Description:</span> </td>
        <td width='80%'> {MAIN_DESCRIPTION}</td>
    </tr>
    <tr>
        <td width='20%' align='left' valign='top'> <span class='signs_b'>Subject:</span> </td>
        <td width='80%'> {MAIN_SUBJECT}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_SUBJECT_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'> <span class='signs_b'>Message:</span> </td>
        <td> {MAIN_MESSAGE}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_MESSAGE_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'> <span class='signs_b'>Substitutions:</span> 
        <span title="Put the cursor to the place where you want to add substitutioins and then click on any from the list. This information will be placed to the email message" class="vtip"><img src='./images/question.png'></span></td>
        <td> {SUBSTITUTIONS}</td>
    </tr>
</table>


</td>
    </tr>
    <tr>
        <td align='center'>
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
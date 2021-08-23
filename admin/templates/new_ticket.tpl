<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td class='page_header'>{MAIN_HEADER}</td></tr>
    <tr><td height='12'></td></tr>
    <tr><td>{MAIN_CONFIRM}</td></tr>
    <tr><td height='12'></td></tr>
</table>

<form action='{MAIN_ACTION}' method='POST'>
<table width='100%' cellspacing='0' cellpadding='2'>
<tr>
    <td width='16%' align='right'><b>Member's ID:</b> </td>
    <td width='84%'>{MAIN_MEMBER} &nbsp; <span class='error'>{MAIN_MEMBER_ERROR}</span> </td>
</tr>
<tr>
    <td width='16%' align='right'><b>Subject:</b> </td>
    <td width='84%'>{MAIN_SUBJECT} &nbsp; <span class='error'>{MAIN_SUBJECT_ERROR}</span> </td>
</tr>
<tr>
    <td valign='top' align='right'><b>Message:</b> </td>
    <td>{MAIN_MESSAGE} &nbsp; <span class='error'>{MAIN_MESSAGE_ERROR}</span> </td>
</tr>
<tr><td colspan='2' height='10'></td></tr>

<tr>
    <td>&nbsp;</td>
    <td>
        <input type='submit' value=" Submit "> &nbsp;
        <input type='reset' value=" Reset "> &nbsp;
        <input type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">
    </td>
</tr>
</table>
<input type='hidden' name='ocd' value='{MAIN_OCD}'>
</form>

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
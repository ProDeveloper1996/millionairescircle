<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>

<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='10%' valign='middle'>News Title:</td>
        <td width='90%'> {MAIN_TITLE} <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td>Date:</td>
        <td> {MAIN_DATE} </td>
    </tr>
    <tr><td style='padding-top:20px; font-size:1.2em;' colspan='2' align='center' >News Text:</td></tr>
    <tr><td colspan='2'>{MAIN_CONTENT}</td></tr>
    <tr><td  colspan='2'>{MAIN_CHECK}</td></tr>
    <tr><td colspan='2'>
            <input class='some_btn' type='submit' value=" Save "> &nbsp;
            <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
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
        <td width='20%' align='left' valign='top'>Category name:</td>
        <td width='80%'> {MAIN_TITLE}</td>
    </tr>
    <tr>
        <td></td>
        <td><span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td align='left' valign='top'>Description:</span> </td>
        <td> {MAIN_DESCRIPTION}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_DESCRIPTION_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'>Members levels: <span title="This category will be visible for members of checked levels only." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_LEVEL}</td>
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
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</H4></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
    <tr>
        <td>

<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td>Category:</td>
        <td> {MAIN_CATEGORY} </td>
    </tr>
    <tr>
        <td width='15%'>Product name:</td>
        <td width='85%'> <input type='text' name='title' value='{MAIN_TITLE}' style='width: 500px;' /> &nbsp; <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td valign='top'>Description:</td>
        <td> {MAIN_DESCRIPTION}</td>
    </tr>
    <tr>
        <td valign='top'>Price:</td>
        <td> $<input type='text' name='price' value='{MAIN_PRICE}' style='width: 100px;' /> </td>
    </tr>
    <tr>
        <td valign='top'>File: <span title="E-Product file for downloading by members after payment." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_FILE} </td>
    </tr>
    <tr>
        <td valign='top'>Image: <span title="Image for the product preview." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_PHOTO} </td>
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
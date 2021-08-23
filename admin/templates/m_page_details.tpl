<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3'>
    <tr>
        <td>
        
<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='25%' valign='middle'>Page Title:</td>
        <td width='75%'> {MAIN_TITLE} <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td>Page name in menu:
        <span title="This text will be shown as a link to the page in additional section of left menus. 
        Try to name it as short as possible." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_TITLE_MENU} <span class='error'>{MAIN_TITLE_MENU_ERROR}</span></td>
    </tr>
    <tr>
        <td style='padding-top:20px; font-size:1.2em;' colspan='2' align='center' >Page Content:</td>
    </tr>
    <tr>
        <td colspan='2'> {MAIN_CONTENT} </td>
    </tr>
    <tr><td>Visible for (levels):
            <span title="You can choose to what exact member level(s) this page will be visible." class="vtip"><img src='./images/question.png'></span>
            {MAIN_LEVELS}</td></tr>
</table>


        </td>
    </tr>
    <tr>
        <td align='center'>
            <input class='some_btn' type='submit' value=" Update "> &nbsp;
            <input class='some_btn' type='button' value=" Close " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
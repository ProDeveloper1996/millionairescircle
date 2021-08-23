<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}</td>
    </tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>
        
        
<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td valign='middle' width='20%'>Page Title:</td>
        <td width='80%'> {MAIN_TITLE} <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td valign='middle'>Page name in menu: 
        <span title="This text will be shown as a link to the page in top menu in public area among the other menus. Try to name it as short as possible." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_TITLE_MENU} <span class='error'>{MAIN_TITLE_MENU_ERROR}</td>
    </tr>
    <tr>
        <td valign='middle'>SEO Keywords: 
        <span title="Input SEO keywords for search engines (coma separated). Leave blank if you do not need to optimize your site." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_KEYWORDS}</td>
    </tr>
    <tr>
        <td valign='middle'>SEO Description:
        <span title="Input SEO description for search engines. Leave blank if you do not need to optimize your site." class="vtip"><img src='./images/question.png'></span></td>
        <td> {MAIN_DESCRIPTION}</td>
    </tr>
    <tr><td style='padding-top:20px; font-size:1.2em;' colspan='2' align='left' >Page Content:</td> </tr>
    <tr><td colspan='2'> {MAIN_CONTENT}

             </td>
    </tr>
</table>


</td>
    </tr>
    <tr>
        <td align='center'>
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
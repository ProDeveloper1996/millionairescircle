<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='message'>{MAIN_MESSAGE}</td></tr>
</table>


<form action='{ACTION_SCRIPT}' method='POST' enctype='multipart/form-data' >
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr><td><H4>Logo</H4></td></tr>
    <tr>
        <td></td>
        <td></td>
        <td>
        <i>Only .png files 57px in height can be uploaded. It is strongly recommended you use logo with transparent background.</i>
    </tr>
    <tr>
        <td style="width: 200px;"><span class='signs_b'>Image logo:</span></td>
        <td></td>
        <td>
        <input type='file' name='photo' value='' style='width: 320px;'> 
         &nbsp; <span class='error'>{MAIN_FILE_ERROR}</span> </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td >
            <input class='some_btn' type='submit' value="Update" style="width: 150px">
            <input type='hidden' name='ocd' value='updatelogo'>
        </td>
    </tr>
</table>
</form>

<form action='{ACTION_SCRIPT}' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr><td height='10' colspan='3'><hr></td></tr>
    <tr><td><H4>Footer</H4></td></tr>
    <tr>
        <td style="width: 200px;"><span class='signs_b'>Text:</span>
        <span title=" The content of this field will be viewed in footer of you site in public are, backoffice and replicated sites
" class="vtip"><img src='./images/question.png'></span> 
</td>
        <td>
            
        </td>
        <td> <textarea name='FooterContent' style=" width: 400px;height: 60px;">{FOOTER_CONTENT}</textarea></td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td >
            <input class='some_btn' type='submit' value="Update" style="width: 150px">
            <input type='hidden' name='ocd' value='updatefooter'>
        </td>
    </tr>
</table>
</form>



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
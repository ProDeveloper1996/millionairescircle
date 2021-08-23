<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td></tr>
</table>

<table border='0' cellspacing='0' cellpadding='5' bgcolor="#F5F5F5" class='w_border'>
    <tr>
        <td class='w_border'>
        <b>Instructions</b><br /><br />

Please prepare file with data for uploading in CSV format. File should use ","
as a separator for fields. Fields can be almost in any order in file,
but the first line of file should contain names of fields.<br /><br />

<b>enroller_id</b> - ID of member's sponsor.<br />
<b>first_name</b> - Member's first name.<br />
<b>last_name</b> - Member's last name.<br />
<b>username</b> - Member's username.<br />
<b>passwd</b> - Member's password.<br />
<b>email</b> - Member's email.<br />
<P>
For example, file may be like this:<br /><br />
enroller_id,first_name,last_name,username,passwd,email<br />
4,Alexander,Smith,Alex,somecat,alex@yahoo.com<br />
1,Maria,Smith,Mat,somedog,mary@yahoo.com<br />
<P>
This file adds 2 new members. Their usernames are Alex and Mat.<br />
<P>
Good luck!
</P>
        </td>
    </tr>
    
    <tr height='30'>
        <td class='w_border' bgcolor="#E7E7E7">

        <form action='{MAIN_ACTION}' method='POST' enctype='multipart/form-data'>
        {MAIN_FILE}&nbsp;&nbsp;<input type="submit" value="Add members from file">&nbsp;&nbsp;<input type='button' value=" Cancel " onClick="window.location.href='members.php'">
        <input type='hidden' name='ocd' value='fromfile'>
        <br />{MAIN_FILE_ERROR}      
        </form>
        </td>
    </tr>

</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
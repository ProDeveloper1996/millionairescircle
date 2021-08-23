<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center' bgcolor='#E7E7E7' class='w_border'>
    <tr  bgcolor='#F5F5F5'>
        <td class='w_border'>

<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='20%'> <span class='signs_b'>Author :</span> </td>
        <td width='80%'> {MAIN_AUTHOR} &nbsp; <span class='error'>{MAIN_AUTHOR_ERROR}</span></td>
    </tr>

    <tr>
        <td width='20%'> <span class='signs_b'>Location :</span> </td>
        <td width='80%'> {MAIN_LOCATION} &nbsp; <span class='error'>{MAIN_LOCATION_ERROR}</span></td>
    </tr>

    <tr>
        <td width='20%' valign='top'> <span class='signs_b'>Testimonial :</span> </td>
        <td width='80%'> {MAIN_DESCRIPTION} &nbsp; <span class='error'>{MAIN_DESCRIPTION_ERROR}</span></td>
    </tr>
    <tr><td colspan='2' height='10'></td></tr>

    <tr>
        <td valign='top'> <span class='signs_b'>Photo :</span> </td>
        <td> {MAIN_PHOTO} </td>
    </tr>
    <tr><td colspan='2' height='10'></td></tr>

</table>

</td>
    </tr>
    <tr>
        <td class='w_border' align='center'>
            <input type='submit' value=" Update "> &nbsp;
            <input type='button' value=" Close " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
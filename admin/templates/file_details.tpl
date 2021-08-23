<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
</table>
<p>

<table width='70%' cellspacing='0' cellpadding='0' align='center' border='0' bgcolor="#F5F5F5" class='w_border'>
    <tr>
        <td class='w_border'>

<form action='{MAIN_ACTION}' method='POST'>
<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'>
    <tr>
        <td width='30%'><span class='signs_b'>The total rows in file : </span></td>
        <td width='70%'>{ALL_ROWS}</td>
    </tr>
    <tr>
        <td><span class='signs_b'>Rows successfully added : </span></td>
        <td>{ALL_SUCCESS}</td>
    </tr>
    <tr>
        <td><span class='signs_b'>Rows failed  : </span></td>
        <td>{ALL_FAIL}</td>
    </tr>
    <tr>
        <td><span class='signs_b'>The next rows are wrong : </span></td>
        <td> {ALL_LIST_MISTAKE} </td>
    </tr>
</table>
        
        </td>
    </tr>
    <tr height="30">
        <td class='w_border' align='center' bgcolor="#E7E7E7">
            <input type='submit' value=" Close " class='some_btn'>
        </td>
    </tr>
</table>    


</form>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
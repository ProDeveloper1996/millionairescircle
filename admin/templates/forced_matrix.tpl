<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border='0' style='width:100%;'>
                <tr style='height:30px;'>
                    <td>
                        <H4>{MAIN_HEADER}</H4>                    
                    </td>
                    <td align='right'>
                        {MAIN_LEVELS}                    
                    </td>
                </tr>  
            </table>
        </td>
    </tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td></tr>
<tr>
    <td>
        <table cellpadding="0" cellspacing="0" align='left' class='f_border' bgcolor="#DCDCE">
            <tr align="left" valign="middle">
                <td align='center'>
                    &nbsp;{MAIN_FIRST_NAME} {MAIN_LAST_NAME}&nbsp;<br> (ID: {MAIN_ID})
                </td>
                <td valign='middle' align='left'>
                    {MAIN_CONTENT}
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4></td></tr>
    <tr><td height='1' bgcolor='#688da8'></td></tr>
    <tr><td height='12'></td></tr>
    <tr><td align='center'><span class='error'>{MAIN_MESS}</span></td></tr>
</table>

<form action='manual.php' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center' bgcolor='#E7E7E7' class='w_border'>
    <tr bgcolor='#F5F5F5'>
        <td class='w_border' align='left'>
            
            <table width='40%' align='left' border='0' cellspacing='0' cellpadding='3'>
                <tr><td colspan='2'></td></tr>
                <tr>
                    <td>
                        <span class='signs_b'>Member ID: <span title="Input the ID of the member you want to pay for manually. By clicking on Pay
                        button you will pay the Entrance Fee for this member and the member will be placed to the matrix." class="vtip"><img src='./images/question.png'></span></span>
                    </td>
                    <td>
                        {MAIN_ID}
                    </td>
                </tr>
                <tr style='height:10px;'><td colspan='2'></td></tr>
                {FORCED_LEVEL}
            </table>

        </td>
    </tr>

    <tr height='30'>
            <td class='w_border' align='left'>
                <input class='some_btn' type='submit' value=' Pay '>
                <input type='hidden' name='ocd' value='pay'>
            </td>
        
    </tr>

</table>
</form>

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='12'></td></tr>
    <tr><td height='20'><H4>{MAIN_HEADER2}</H4></td></tr>
    <tr><td height='12'></td></tr>
</table>

<form action='manual.php' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center' bgcolor='#E7E7E7' class='w_border'>
    <tr bgcolor='#F5F5F5'>
        <td class='w_border' align='left'>
            
            <table width='40%' align='left' border='0' cellspacing='0' cellpadding='3'>
                <tr><td colspan='2'></td></tr>
                <tr>
                    <td>
                        <span class='signs_b'>Member ID: <span title="Input the ID of the member you want to pay for a E-Shop product manually. By clicking on Pay
                        button you will pay for the product manually for entered member." class="vtip"><img src='./images/question.png'></span></span>
                    </td>
                    <td>
                        {MAIN_ID}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='signs_b'>E-shop Product ID: <span title="Input the ID of the product from your E-Shop." class="vtip"><img src='./images/question.png'></span></span>
                    </td>
                    <td>
                        {MAIN_PRODUCT}
                    </td>
                </tr>
                
            </table>

        </td>
    </tr>

    <tr bgcolor='#E7E7E7' height='30'>
            <td class='w_border' align='left'>
                <input class='some_btn' type='submit' value=' Pay ' />
                <input type='hidden' name='ocd' value='product' />
            </td>
        
    </tr>

</table>
</form>
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
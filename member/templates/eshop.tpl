<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

        <p class='text'>
        {MAIN_CATEGORY_DESCRIPTION}    
        </p>
        <table align='center' border='0' cellpadding="0" cellspacing="0" width='100%'>
                <tr valign='top'>
                <!-- BEGIN: PRODUCTS_ROW -->

                {ROW_UP}
                    <table border='0' cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                            <td style='text-align:center;'>
                                {ROW_TITLE}
                            </td>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                        </tr>  
                        <tr>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                            <td align='center'>
                            {ROW_PHOTO}
                            </td>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                            <td style='text-align:center;'>
                                <span class='question' style='color:#d6912c;'>{ROW_PRICE}</span>
                            </td>
                            <td>
                                <img src='./images/dot.gif' width='5' border='0'>
                            </td>
                        </tr>                          
                    </table>
                    {ROW_DOWN}
                    <!-- END: PRODUCTS_ROW -->
                </tr>
            </table>
            
<div class="form-group" style="margin-top:20px">
    <div class="row">
        {MAIN_PAGES}
    </div>    
</div>      
            

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
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
                            <td align='center'>
                                <span class='questionSmall'>{ROW_TITLE}</span>
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
                            <td align='center'>
                                <span class='questionSmall' style='color:#e71715;'>{ROW_PRICE}</span>
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
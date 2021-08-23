<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}


<h2>{MAIN_HEADER}</h2>
        
            <table width="100%" cellpadding="4" cellspacing="0" border='0'>

                <tr><td class="w_padding"><span class='question'> Payment for :</span></td><td class="w_padding"><span class='answer'>{PRODUCT_NAME}</span></td></tr>

                <tr><td class="w_padding"><span class='question'> Level :</span></td><td class="w_padding"><span class='answer'>{LEVEL}</span></td></tr>
                <tr><td class="w_padding"><span class='question'> Processor :</span></td><td class="w_padding"><span class='answer'>{PROCESSOR}</span></td></tr>
                <tr><td class="w_padding"><span class='question'> Processor fee :</span></td><td class="w_padding"><span class='answer'>{PROCESSOR_FEE}</span></td></tr>
                <tr><td class="w_padding"><span class='question'> Amount :</span></td><td class="w_padding"><span class='answer'>{CURRENCY}{AMOUNT}</span></td></tr>
                <tr><td class="w_padding"><span class='question'> Total amount :</span></td><td class="w_padding"><span class='answer'>{DETAILS}</span></td></tr>
                <tr style='height:10px;'><td colspan='2'></td></tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <table cellpadding="0" cellspacing="0" border='0'>
                            <tr>
                                <td>
                                    {CODE}        
                                </td>
                                <td style="padding-left:5px;">
                                    <button type="submit" class="btn btn-form" onClick="window.location.href='payment_f.php'"> Cancel </button>     
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr>
            </table>
               

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
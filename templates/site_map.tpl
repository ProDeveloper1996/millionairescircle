
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-stack-1x">i</i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row">

        <table border="0" cellpadding="0" cellspacing="10" align='center' style="width:100%;height:41px;" >
            <!-- BEGIN: TABLE_ROW -->
            <tr>
                <td>
                    <img src='./images/page.gif' align='absmiddle' border='0' title='{ROW_TITLE}' alt='{ROW_TITLE}' />&nbsp;&nbsp;<a href='{ROW_LINK}'>{ROW_TITLE}</a>
                </td>
            </tr>
            <!-- END: TABLE_ROW -->
            <tr>
                <td>
                    <img src='./images/page.gif' align='absmiddle' border='0' title='{DICT.SM_SupportCenter}' alt='{DICT.SM_SupportCenter}' />&nbsp;&nbsp;<a href='ticket.php'>{DICT.SM_SupportCenter}</a>
                </td>
            </tr>
            <tr>
                <td>
                    <img src='./images/page.gif' align='absmiddle' border='0' title='{DICT.SM_FAQ}' alt='{DICT.SM_FAQ}' />&nbsp;&nbsp;<a href='faq.php'>{DICT.SM_FAQ}</a>
                </td>
            </tr>
        </table>      

        </div>
    </div>
        
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
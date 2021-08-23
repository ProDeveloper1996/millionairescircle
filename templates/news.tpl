<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
<i class="fa fa-align-justify fa-stack-1x"></i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row">
        
            <!-- BEGIN: NEWS_ROW -->
<table width="100%" cellpadding="0" cellspacing="0" border='0' align='center'>
    <tr style='height:60px;'>
        <td>
            <table width="100%" cellpadding="5" cellspacing="0" border='0' align='center'>
                <tr>
                    <td valign='top' >
                        {ROW_PHOTO}
                    </td>
                    <td width='100%' valign='top' class="w_padding">
                        <div class="colaps-nav collapsed" style="cursor: auto">{ROW_TITLE}</div>
		<span class='question'>{ROW_DATE}</span><br />
                        {ROW_DESCRIPTION}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
    <!-- END: NEWS_ROW -->
    <!-- BEGIN: NEWS_EMPTY -->
<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr>
        <td class='w_border' align='center'>{DICT.News_NoNews}</td>
    </tr>
</table>
    <!-- END: NEWS_EMPTY -->   

   
        </div>
    </div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
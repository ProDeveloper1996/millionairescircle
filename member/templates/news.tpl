<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

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
   

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
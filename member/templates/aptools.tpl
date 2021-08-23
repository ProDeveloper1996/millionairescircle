<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>


            <table width='100%' border='0' cellspacing='0' cellpadding='4' class="simple-little-table">
    
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td colspan='3'>{ROW_PAGE}</td>
    </tr>
    <tr>
        <td width='50%'><a href='{ROW_TARGET_URL}' target='_blank'>{ROW_PHOTO}</a></td>
<td width='40%'><textarea style='height:100%;width:100%;' name='some'>
<a href='{ROW_TARGET_URL}' target='_blank'><img src='{ROW_IMAGE_URL}' alt='{ROW_TEXT}' title='{ROW_TEXT}' border='0' /></a>
</textarea>
        </td>
    </tr>
    <!-- END: TABLE_ROW -->
    
            </table>



{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
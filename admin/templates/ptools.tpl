<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="List of Banners created by your members with the ability to administrate it" class="vtip"><img src='./images/question.png'></span></td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_OWNER}</td>
        <td>Banner image</td>
        <td>Code for embedding</td>
        <td>Action</td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td>{ROW_OWNER}</td>
        <td width-'30%'>{ROW_OBJECT}</td>
        <td>
            <textarea style='height:80px;width:700px;' name='some'>{ROW_OBJECT}</textarea>
        </td>
        <td width='25'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor="{ROW_BGCOLOR}">
        <td colspan='4' align='center'>The list is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}         

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
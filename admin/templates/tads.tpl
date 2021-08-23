<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="List of Text Ads created by your members with the ability to administrate it" class="vtip"><img src='./images/question.png'></span></td></tr>
    </table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>    
    <tr><td height='20'>{MAIN_CONFIRM}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td>{HEAD_MEMBER}</td>
        <td>{HEAD_CONTENT}</td>
        <td width='100'>{HEAD_DISPLAYED}</td>
        <td width='25'>&nbsp;</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor="{ROW_BGCOLOR}">
        <td>{ROW_ID}</td>
		<td>{ROW_MEMBER}</td>
		<td>{ROW_CONTENT}</td>
		<td>{ROW_DISPLAYED}</td>
        <td>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='w_border' colspan='5' align='center'>The list of text ads created by your members is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
 {MAIN_PAGES}


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
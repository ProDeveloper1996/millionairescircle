<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='70%' border='0' cellspacing='0' cellpadding='3' class='filter'>
    <tr><td>Filter</td></tr>
    <tr><td>        
            <form method='POST'>
                <td width='40%'>{MAIN_CAT_SELECT} </td>
                <td width='40%'><input type='text' name='search' value='{SEARCH}' maxlength='50' /> </td>
                <td width='20%'><input class='some_btn' type='submit' value=" Find " /> </td>
                <input type='hidden' name='filter' value='1' />
            
            
        </td>
      </tr>
</table>
</form>
<table width='100%' border='0' cellspacing='0' cellpadding='4' align='right'>
<tr> 
<td>{MAIN_ADDLINK}</td>
</tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td>{HEAD_TITLE}</td>
        <td>{HEAD_FILE}</td>
        <td>{HEAD_PRICE}</td>
        <td> Visible for ({HEAD_CATEGORY})</td>
        
        <td colspan='3'>Action</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='5%'>{ROW_ID}</td>
        <td>{ROW_TITLE}</td>
        <td>{ROW_FILE}</td>
        <td>{CURRENCY}{ROW_PRICE}</td>
        <td>{ROW_CATEGORY}</td>
        <td width='5%'><div id='resultik{ROW_ID}'>{ROW_ACTIVELINK}</div></td>
        <td width='5%'>{ROW_EDITLINK}</td>
        <td width='5%'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='8' align='center'>The list of products is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
 {MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
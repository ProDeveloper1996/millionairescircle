<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
        

<table width='100%' border='0' cellspacing='0' cellpadding='0' class="simple-little-table" >
    <tr bgcolor='#475567'>
        <td class='b_border' align='center' width='90'>{HEAD_MEMBER_ID}</td>
        <td class='b_border' align='center'>{HEAD_USERNAME}</td>
        <td class='b_border' align='center'>{HEAD_M_LEVEL}</td>
        <td class='b_border' align='center'>{HEAD_REG}</td>
        <td class='b_border' align='center'><b class='pages'>{HEAD_SPONSORS}</b></td>
        <td class='b_border' align='center' width='20'><b class='pages'>A</b></td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td class='b_border' align='center'>{ROW_MEMBER_ID}</td>
        <td class='b_border'>&nbsp;{ROW_USERNAME}</td>
        <td class='b_border'>&nbsp;{ROW_M_LEVEL}</td>
        <td class='b_border' align='center'>&nbsp;{ROW_REG}</td>
        <td class='b_border' align='center'>{ROW_SPONSORS}</td>
        <td class='b_border' align='center' width='20'>{ROW_EMAIL}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td class='b_border' colspan='6' align='center'>The list of sponsors is empty.</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>

<div class="form-group" style="margin-top:20px">
    <div class="row">
        {MAIN_PAGES}
    </div>    
</div> 

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}</td>
    </tr>
</table>

        <table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
            <tr>
                <td>{HEAD_NAME}</td>
                <td>{HEAD_CODE}</td>
                <td>{HEAD_ACCOUNTID}</td>
                <td>{HEAD_ROUTINE}</td>
                <td>{HEAD_FEE}</td>
                <td colspan='2'>Actions</td>
            </tr>

            <!-- BEGIN: TABLE_ROW -->
            <tr>
                <td>{ROW_NAME} </td>
                <td>{ROW_CODE} </td>
                <td align='center'>{ROW_ACCOUNTID} &nbsp;</td>
                <td>{ROW_ROUTINE} &nbsp;</td>
                <td>{ROW_FEE}% &nbsp;</td>
                <td>{ROW_ACTIVELINK}</td>
                <td>{ROW_EDITLINK}</td>
            </tr>
            <!-- END: TABLE_ROW -->

            <!-- BEGIN: TABLE_EMPTY -->
            <tr>
                <td class='w_border' colspan='6' align='center'>There are no processors in database.</td>
            </tr>
            <!-- END: TABLE_EMPTY -->
        </table>
<p style='padding-top:20px'>To be able to receive payments from your members you should activate and set (click on Edit icon to set the processor) those processors you are going to use. Be sure you have verified account there.</p> 
<p>All members' payments are automatically registered in the system if they use any of active and set processors in the system.</p>
<p><a target='blank' href='http://runmlm.com/content.php?p_id=20'>Read more about Payment processors</a></p>
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
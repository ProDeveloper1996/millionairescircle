<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td>
    </tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td rowspan='2'>{HEAD_TITLE}</td>
        <td>{HEAD_FEE}</td>
        <td rowspan='2'>Action</td>
   </tr>
   <tr>
        <td>{HEAD_NAMES}</td>
    </tr>

    {MAIN_CONTENT}

    <tr>
        <td colspan='4'>
            <form>
                <input class='some_btn' type='submit' value="Clear Fees">
                <input type='hidden' name='ocd' value='clear_fee'>
            </form>
        </td>
    </tr>
</table>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
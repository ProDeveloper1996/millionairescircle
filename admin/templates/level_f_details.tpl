<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
      <td>{MAIN_HEADER}</td>
    </tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>


<form action='{MAIN_ACTION}' method='POST' enctype='multipart/form-data'>
<table border='0' cellspacing='0' cellpadding='2' align='left'>
    <tr>
        <td> <span class='signs_b'>Title:</span> </td>
        <td> {MAIN_TITLE} &nbsp; <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr>
        <td> <span class='signs_b'>Paid Levels:</span> </td>
        <td> {MAIN_DEPTH} &nbsp; <span class='error'>{MAIN_DEPTH_ERROR}</span></td>
    </tr>

    <tr>
        <td> <span class='signs_b'>Cost ($):</span> </td>
        <td> {MAIN_COST} &nbsp; <span class='error'>{MAIN_COST_ERROR}</span></td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td align='center'>
            <input class='some_btn' type='submit' value=" Update "> &nbsp;
            <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
    </form>
        </td>
    </tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
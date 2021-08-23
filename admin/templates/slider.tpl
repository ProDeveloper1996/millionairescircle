<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<form action='{ACTION_SCRIPT}' method='POST'>
    <table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
        <tr>
            <td width='400'><span class='signs_b'>Time:</span></td>
            <td width='20'>
                <span title="Time" class="vtip"><img src='./images/question.png'></span>
            </td>
            <td>
                <input type='text' name='time' value='{MAIN_TIME}' style='width:100px;'> &nbsp; <span class='error'>{MAIN_TIME_ERROR}</span>

                <input class='some_btn' type='submit' value="Update" style="width: 120px;">
                <input type='hidden' name='ocd' value='update'>

            </td>
        </tr>
    </table>
</form>
<hr>
<p></p>

<a href='?add' title='Add a slide'><img src='./images/add.png' border='0'></a>

<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <!-- BEGIN: LIST_ROW -->
    <tr>
        <td width="10%"><img src="/data/slider/{ROW_NAME}" width="100"></td>
        <td width="80%">{ROW_TEXT}</td>
        <td width='10%'>
            <a href='?edit={ROW_NAME}'><img src='./images/edit.png' width='25' border='0' alt='Edit'></a>
            <a href='?ocd=del&id={ROW_NAME}' onClick="return confirm ('Do you really want to delete this file?');"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>
        </td>
    </tr>
    <!-- END: LIST_ROW -->
</table>

    <!-- BEGIN: ADD -->
<br><br>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>Add slide</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td colspan="2">
		<form action='/admin/slider.php' method='POST'  enctype='multipart/form-data'>
			<input type='file' name='file' value='' style='width: 320px;'>
			
			<input type='hidden' name='ocd' value='add'>
		Only png, jpg, gif files. Size should be 1280*500px.<br>
                {EDITOR}
                <br>
                <input class='some_btn' type='submit' value=" Add " style="padding: 6px 15px; width: initial;"> &nbsp;
                <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='/admin/slider.php'" style="padding: 6px 15px; width: initial;" >
        </form>
	</td>
        <td width='5%'></td>
    </tr>
</table>
   <!-- END: ADD -->

    <!-- BEGIN: EDIT -->
<br><br>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>Edit slide</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td colspan="2">
        <form action='/admin/slider.php' method='POST'  enctype='multipart/form-data'>
            <input type='file' name='file' value='' style='width: 320px;'>
            
            <input type='hidden' name='ocd' value='edit'>
            <input type='hidden' name='id' value='{EDIT_ID}'>
            
        Only png, jpg, gif files. Size should be 1280*500px.<br>
                {EDITOR}
                <br>
                <input class='some_btn' type='submit' value=" Save " style="padding: 6px 15px; width: initial;"> &nbsp;
                <input class='some_btn' type='button' value=" Cancel " onClick="window.location.href='/admin/slider.php'" style="padding: 6px 15px; width: initial;" >
        </form>
    </td>
        <td width='5%'></td>
    </tr>
</table>
   <!-- END: EDIT -->

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->

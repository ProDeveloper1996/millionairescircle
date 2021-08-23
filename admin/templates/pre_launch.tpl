<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr>
        <td>{MAIN_HEADER}</td>
    </tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
        <td class='message'>{MAIN_MESSAGE}</td>
    </tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3'>

    <tr>
        <td align='left'>
            <H4>Size Settings</H4>
        </td>
    </tr>
    <form name='set' action='{MAIN_ACTION}' method='POST'>
        <tr>
            <td align='left'>
                <table border='0' cellspacing='0' cellpadding='5'>
                    <tr>
                        <td>
                            Pre-Launch Status:
                        </td>
                        <td>
                            {PRE_LAUNCH}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Launch Date:
                        </td>
                        <td>
                            {PRE_LAUNCH_DATE}
                        </td>
                    </tr>

                    <tr>
                        <td ><span class='signs_b'>Time after Launch:</span></td>
                        <td >
                            <input type="text" name="time_after_launch" value="{TIME_AFTER_LAUNCH}" maxlength="6" style="width:30px;"> hour
                        </td>
                    </tr>

                </table>
            </td>
        </tr>



        <tr>
            <td align='center'>
                <input class='some_btn' type='submit' value="Update">
                <input type='hidden' name='ocd' value='update'>
            </td>

    </form>
    </tr>
</table>
<br/>

{FORCED_SETTINGS}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
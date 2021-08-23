<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td class='message'>{MAIN_MESSAGE}</td></tr>
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
                        <span class='signs_b'>Width:</span>
                    </td>
                    <td>
                        {WIDTH}&nbsp;<span class='error'>{WIDTH_ERROR}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='signs_b'>Depth:</span>
                    </td>
                    <td>
                        {DEPTH}&nbsp;<span class='error'>{DEPTH_ERROR}</span>
                    </td>
                </tr>
                </table>
            </td>
    </tr>
    <tr><td><hr></td></tr>
    <tr>
        <td align='left'>
            <h4>Payment Settings</h4>
        </td>
    </tr>

    <tr>
        <td align='left'>
            <table border='0' cellspacing='0' cellpadding='5'>
                <tr>
                    <td>
                        <span class='signs_b'>Period between payments:</span>
                    </td>
                    <td>
                        {MONTH_PERIOD} days &nbsp;<span class='error'>{MONTH_PERIOD_ERROR}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='signs_b'>Payment period:</span>
                    </td>
                    <td>
                        {PAY_PERIOD} days &nbsp;<span class='error'>{PAY_PERIOD_ERROR}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='signs_b'>Notification period:</span>
                    </td>
                    <td>
                        {WARN_PERIOD} days &nbsp;<span class='error'>{WARN_PERIOD_ERROR}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
   <tr><td><hr></td></tr>
    <tr>
        <td align='left'>
            <h4>Compression Matrix</h4>
        </td>
    </tr>
    <tr>
            <td align='left'>
                {MATRIX_MODE} &nbsp;<span class='error'>{MATRIX_MODE_ERROR}</span>
            </td>
    </tr>
    <tr><td><hr></td></tr>
    <tr>
        <td align='left'>
            <h4>Payment Mode </h4>
        </td>
    </tr>
    <tr>
            <td align='left'>
                <table border='0' cellspacing='0' cellpadding='5'>
                <tr>
                    <td>
                        {PAY_MODE} &nbsp;{MAIN_P_DATA}    
                    </td>
                </tr>
                <tr>
                    <td>
                        {USE_BALANCE} Allow members to use their account cash balance for upgrade
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
            <td align='center'>
                <input class='some_btn' type='submit' value="Update">
                <input type='hidden' name='ocd' value='update_forced'>
            </td>
        
        </form>
    </tr>
</table>
<br />

{FORCED_SETTINGS}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
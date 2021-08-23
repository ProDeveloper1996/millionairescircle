<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

            <table width="100%" cellpadding="3" cellspacing="0" border='0'>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Registration Date:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_REGISTRATION}</span>
                    </td>
                </tr>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Last Access:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_LAST_ACCESS}</span>
                    </td>
                </tr>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Your Account ID:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_ID}</span>
                    </td>
                </tr>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Your Referral Link:</span>
                    </td>
                    <td class='w_padding' style='padding-left:2px;'>
                        {ACCOUNT_LINK}
                    </td>
                </tr>
                <tr>
                    <td class='w_padding' valign='top'>
                        <span class='question'>{ACCOUNT_LANDS_TITLE}</span>
                    </td>
                    <td class='w_padding'>
                        {ACCOUNT_LANDS}
                    </td>
                </tr>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Your Enroller's ID:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_ENROLLER}</span>
                    </td>
                </tr>

                <tr>
                    <td class='w_padding'>
                        <span class='question'> {ACCOUNT_DOWNLINES_TITLE}</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_DOWNLINES}</span> {DOWNLINES_LINK}
                    </td>
                </tr>

                <tr>
                    <td class='w_padding'>
                        <span class='question'> Sponsored Members:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_SPONSORS}</span> [ <a class='smallLink' href='sponsors.php'>Details</a> ]
                    </td>
                </tr>
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Your Level:</span>
                    </td>
                    <td class='w_padding'>
                        <span class='answer'>{ACCOUNT_LEVEL}</span> {ACCOUNT_UPGRADE}
                    </td>
                </tr>
                {ACCOUNT_STATUS}
                <tr>
                    <td class='w_padding'>
                        <span class='question'> Amount earned:</span>
                    </td>
                    <td class='w_padding'>
                        <a href='./cash.php' class="smallLink">{CURRENCY}{ACCOUNT_CASH}</a>
                    </td>
                </tr>
            </table>   


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
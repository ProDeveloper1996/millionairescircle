<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>
        
<div class="container">
            
     <div style='text-align:right;'>
        <a href='m_levels.php'><img alt='Show Levels' title='Show Levels' src='./images/levels_icon.png' valign='middle' border='0' /></a>
     </div>   
               
            
        <table width="100%" cellpadding="0" cellspacing="0" align='center' class='f_border' border='0' style='margin-top:10px;'>
            <tr valign="middle">
                <td style='text-align:center;'>
                    <span class='question'>{MAIN_FIRST_NAME} {MAIN_LAST_NAME}</span><br /> <span class='answer'>(ID: {MAIN_ID})</span>
                </td>
                <td valign='middle' align='left'>
                    {MAIN_CONTENT}
                </td>
            </tr>
        </table>   
</div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
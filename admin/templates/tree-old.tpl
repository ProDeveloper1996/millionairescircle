<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td>
        <td align='right'><div class="spoiler_style" onClick="open_close('spoiler1')">
         HELP GUIDE</div></td>
    </tr>
</table>
<table width='100%'>
    <tr>
      <td><div id="spoiler1" style="display:none;">Here you can see the overall SPONSORSHIP of members in the system. This is NOT the matrix tree.</div></td>
    </tr>
</table> 
<table width='100%' border='0' cellspacing='0' cellpadding='0' class="simple-little-table">
<tr>
    <td>
        <a href='./tree.php' class="inactive2">Collapse All</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href='javascript: void (0);' class="inactive2" onclick='expand ();'>Expand All</a>

        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
            <td>
				<div id='result'>{RESULT}</div>
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
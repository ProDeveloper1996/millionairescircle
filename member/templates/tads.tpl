<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}</h2>

<span class='answer'>{DICT.TD_Text1} <b>{NUMBER}</b> {DICT.TD_Text2}</span><br />
 
{DICT.TD_Text3}  "{SITE_TITLE}"<br />
{DICT.TD_Text4} <b>{SHOW_P}</b> {DICT.TD_Text5} <b>{SHOW_M}</b> {DICT.TD_Text6}

<div style="text-align:right;">
{MAIN_ADDLINK}
</div>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr bgcolor='#475567'>
        <th class='b_border' align='center'><b class='pages'>{DICT.TD_hTitle}</b></th>
        <th class='b_border' align='center'><b class='pages'>{DICT.TD_hContent}</b></th>
        <th class='b_border' align='center'><b class='pages'>{DICT.TD_hDisplayed}</b></th>
        <th class='b_border' align='center' colspan='2' width='40'><b class='pages'>{DICT.TD_hAction}</b></th>

    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td class='b_border' align='center'>{ROW_TITLE}</td>
        <td class='b_border'>{ROW_CONTENT}</td>
        <td class='b_border' align="center">{ROW_DISPLAYED}</td>
        <td class='b_border' align='center' width='20'>{ROW_EDITLINK}</td>
        <td class='b_border' align='center' width='20'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td class='b_border' colspan='5' align='center'>{DICT.TD_ListEmpty}</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
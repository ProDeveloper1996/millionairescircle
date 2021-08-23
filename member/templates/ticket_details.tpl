<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}


<h2><a href="./tickets.php">{MAIN_HEADER}</a> / {MAIN_SUBJECT}</h2>

<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'>
    <tr>
        <td>
        <table width='100%' cellspacing='0' cellpadding='2' class='simple-little-table'>
            <tr>
                <td width="20%"><b>{DICT.TN_Status}:</b></td>
                <td>{MAIN_STATUS}</td>
            </tr>
            <tr>
                <td><b>{DICT.TN_CreatedOn}:</b></td>
                <td>{MAIN_DATE_CREATE}</td>
            </tr>
            <tr>
                <td><b>{DICT.TN_LastUpdate}:</b></td>
                <td>{MAIN_LAST_UPDATE}</td>
            </tr>
        </table>
        </td>
    </tr>
</table>

<table width='100%' cellspacing='5' cellpadding='5' class='simple-little-table'>
    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width="20%" rowspan="2"><b>{ROW_FROM}</b></td>
        <td><b>{DICT.TN_Postedon} {ROW_DATE_POST}</b></td>
    </tr>
    <tr>
        <td>{ROW_MESSAGE}</td>
    </tr>
    <!-- END: TABLE_ROW -->
</table>

{MAIN_FORM}
            

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>

<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='15%'> <span class='signs_b'>Page Title:</span> </td>
        <td width='85%'> {MAIN_TITLE} &nbsp; <span class='error'>{MAIN_TITLE_ERROR}</span></td>
    </tr>
    <tr><td colspan='2' height='10'></td></tr>
    <tr>
        <td valign='top'> <span class='signs_b'>Page Content:</span> </td>
        <td valign='top'>{MAIN_CONTENT}
            <script language='JavaScript'>
                var oEdit1 = new InnovaEditor ("oEdit1");
    
                oEdit1.width=600;
                oEdit1.height=400;

                oEdit1.btnPrint=false;
                oEdit1.btnLTR=true;
                oEdit1.btnRTL=true;
                oEdit1.btnSpellCheck=false;
                oEdit1.btnStrikethrough=true;
                oEdit1.btnSuperscript=true;
                oEdit1.btnSubscript=true;
                oEdit1.btnClearAll=true;
                oEdit1.btnSave=false;
                oEdit1.btnStyles=true;

                /***************************************************
                  ENABLE ASSET MANAGER ADD-ON
                ***************************************************/
                oEdit1.cmdAssetManager = "modalDialogShow('../assetmanager/assetmanager.php',640,465)";

                /***************************************************
                  SETTING EDITING MODE
                  Possible values:
                    - "HTMLBody" (default) 
                    - "XHTMLBody" 
                    - "HTML" 
                    - "XHTML"
                ***************************************************/
                oEdit1.mode="HTMLBody";

    
                oEdit1.REPLACE ("content");
            </script>

        </td>
    </tr>
    <tr><td colspan='2' height='5'></td></tr>
    <tr>
        <td width='15%'> <span class='signs_b'>Page Image (link to sign up):</span> </td>
        <td width='85%'> {MAIN_PHOTO} &nbsp; <span class='error'>{MAIN_PHOTO_ERROR}</span></td>
    </tr>
</table>




</td>
    </tr>
    <tr>
        <td align='center'>
            <input class='some_btn' type='submit' value=" Update "> &nbsp;
            <input class='some_btn'  type='button' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">

            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
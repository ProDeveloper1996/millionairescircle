<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr><td height='20'><H4>{MAIN_HEADER}</H4><hr></td></tr>

</table>

<table width='100%' border='0' cellspacing='0' cellpadding='3' align='center'>
    <tr>
        <td>
        
<form action='{MAIN_ACTION}' method='POST'  enctype='multipart/form-data'>
<table width='100%' border='0' cellspacing='0' cellpadding='2'>
    <tr>
        <td width='20%' align='left' valign='top'>Page Title:</td>
        <td width='80%'> {MAIN_TITLE}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_TITLE_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'> <span class='signs_b'>Page name in menu:</span> </td>
        <td> {MAIN_TITLE_MENU}</td>
    </tr>
    <tr><td></td><td><span class='error'>{MAIN_TITLE_MENU_ERROR}</span></td></tr>
    <tr>
        <td align='left' valign='top'>
            <span class='signs_b'>Page Content:</span>
        </td>
        <td> {MAIN_CONTENT}
        <script language='JavaScript'>
                var oEdit1 = new InnovaEditor ("oEdit1");
    
                oEdit1.width=800;
                oEdit1.height=500;

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
</table>


        </td>
    </tr>
    <tr>
        <td align='center'>
            <input type='submit' class='some_btn' value=" Update "> &nbsp;
            <input type='button' class='some_btn' value=" Cancel " onClick="window.location.href='{MAIN_CANCEL_URL}'">
            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
            <input type='hidden' name='id' value='{MAIN_ID}'>
            </form>
        </td>
    </tr>
</table>
{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
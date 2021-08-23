<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        $this->orderDefault = "name";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/processors.tpl";
        $this->pageTitle = "Processor list";
        $this->pageHeader = "Processor list";
        $this->javaScripts = $this->GetJavaScript ();
        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_NAME" => $this->Header_GetSortLink ("name", "Name"),
            "HEAD_CODE" => $this->Header_GetSortLink ("code", "Code"),
            "HEAD_ACCOUNTID" => $this->Header_GetSortLink ("account_id", "Account ID"),
            "HEAD_FEE" => $this->Header_GetSortLink ("fee", "Fee"),
            "HEAD_ROUTINE" => $this->Header_GetSortLink ("routine_url", "Routine URL"),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );

        $bgcolor = "";
        $result = $this->db->ExecuteSql ("Select * From {$this->object} Order By {$this->orderBy} {$this->orderDir}", true);
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['processor_id'];
            $name = $row['name'];
            $code = $row['code'];
            $account_id = $row['account_id'];
            $fee = $row['fee'];
            $routine_url = $row['routine_url'];
            $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"processor_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' title='Change activity status'></a>";
            $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' title='Edit'></a>";

            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            
            $this->data ['TABLE_ROW'][] = array (
                "ROW_NAME" => $name,
                "ROW_CODE" => $code,
                "ROW_ACCOUNTID" => $account_id,
                "ROW_FEE" => $fee,
                "ROW_ROUTINE" => $routine_url,

                "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                "ROW_EDITLINK" => $editLink,
                "ROW_BGCOLOR" => $bgcolor
            );
        }
        $this->db->FreeSqlResult ($result);
    }

    //--------------------------------------------------------------------------
    function fill_form ($opCode = "update", $source = FORM_FROM_DB)
    {
        $this->mainTemplate = "./templates/processor_details.tpl";

        $id = $this->GetGP ("id");
        switch ($source)
        {
            case FORM_FROM_DB:
                $result = $this->db->ExecuteSql ("Select * From {$this->object} Where processor_id=$id");
                if ($row = $this->db->FetchInArray ($result))
                {
                    $name = "<input type='text' name='name' value='".$row['name']."' maxlength='120' style='width:200px;'>";
                    $code = $row['code'];
                    $account_id = "<input type='text' name='account_id' value='".$row['account_id']."' maxlength='120' style='width:200px;'>";
                    $routine_url = "<input type='text' name='routine_url' value='".$row['routine_url']."' maxlength='250' style='width:200px;'>";
                    $fee = "<input type='text' name='fee' value='".$row['fee']."' maxlength='50' style='width:30px;'>";
                    $extra_field = "<input type='text' name='extra_field' value='".$row['extra_field']."' maxlength='250' style='width:200px;'>";

                    $password = "<input type='password' name='password' value='' maxlength='120' style='width:200px;'>";

                    if ($code == "egold")
                    {
                        $egold_field = split ("\|\|", $row['extra_field']);
                        $egold_altpass = ($egold_field[0] != "") ? "<input type='password' name='altpass' value='********' maxlength='120' style='width:200px;'>" : "<input type='password' name='altpass' value='' maxlength='120' style='width:200px;'>";
                        $egold_mainpass = ($egold_field[1] != "") ? "<input type='password' name='mainpass' value='********' maxlength='120' style='width:200px;'>" : "<input type='password' name='mainpass' value='' maxlength='120' style='width:200px;'>";
                    }
                    
                    if ($code == "libertyreserve")
                    {
                        $lr_field = split ("\|\|", $row['extra_field']);
                        $lr_store = "<input type='text' name='lr_store' value='".$lr_field[0]."' maxlength='50' style='width:200px;'>";
                        $lr_security_word = ($lr_field[1] != "") ? "<input type='password' name='lr_security_word' value='********' maxlength='120' style='width:200px;'>" : "<input type='password' name='lr_security_word' value='' maxlength='120' style='width:200px;'>";
                    }
                    
                }
                else {
                    $this->Redirect ($this->pageUrl);
                }
                break;

            case FORM_FROM_GP:
                $name = "<input type='text' name='name' value='".$this->GetGP ("name")."' maxlength='120' style='width:200px;'>";
                $code = $this->db->GetOne ("Select code From {$this->object} Where processor_id=$id");
                $account_id = "<input type='text' name='account_id' value='".$this->GetGP ("account_id")."' maxlength='120' style='width:200px;'>";
                $routine_url = "<input type='text' name='routine_url' value='".$this->GetGP ("routine_url")."' maxlength='250' style='width:200px;'>";
                $fee = "<input type='text' name='fee' value='".$this->GetGP ("fee")."' maxlength='50' style='width:80px;'>";
                $extra_field = "<input type='text' name='extra_field' value='".$this->GetGP ("extra_field")."' maxlength='250' style='width:200px;'>";
                $egold_altpass = "<input type='password' name='altpass' value='".$this->GetGP ("altpass")."' maxlength='120' style='width:200px;'>";
                $egold_mainpass = "<input type='password' name='mainpass' value='".$this->GetGP ("mainpass")."' maxlength='120' style='width:200px;'>";
                
                $lr_store = "<input type='text' name='lr_store' value='".$this->GetGP ("lr_store")."' maxlength='50' style='width:200px;'>";
                $lr_security_word = "<input type='password' name='lr_security_word' value='".$this->GetGP ("lr_security_word")."' maxlength='120' style='width:200px;'>";
                $strict_code = "<input type='text' name='strict_code' value='".$this->GetGP ("strict_code")."' maxlength='50' style='width:200px;'>";

                $password = "<input type='password' name='password' value='' maxlength='120' style='width:120px;'>";
                break;

            case FORM_EMPTY:
            default:
                $this->Redirect ($this->pageUrl);
                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,

            "MAIN_NAME" => $name,
            "MAIN_NAME_ERROR" => $this->GetError ("name"),
            "MAIN_CODE" => $code,
            "MAIN_ACCOUNT_ID" => $account_id,
            "MAIN_ACCOUNT_ID_ERROR" => $this->GetError ("account_id"),
            "MAIN_ROUTINEURL" => $routine_url,
            "MAIN_ROUTINEURL_ERROR" => $this->GetError ("routine_url"),
            "MAIN_FEE" => $fee,
            "MAIN_FEE_ERROR" => $this->GetError ("fee"),
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
            "MAIN_PASSWORD" => $password,
            "MAIN_PASSWORD_ERROR" => $this->GetError ("password"),
        );


        if ($code == "alertpay")
        {
            $this->data ["MAIN_ALERTPAY"] = array (
                "MAIN_SECURECODE" => $extra_field,
                "MAIN_SECURECODE_ERROR" => $this->GetError("extra_field"),
            );
        }

        if ($code == "libertyreserve")
        {
            $this->data ["MAIN_LR_STORE"] = array (
                "LR_STORE" => $lr_store,
            );
            $this->data ["MAIN_LR_SECUREWORD"] = array (
                "LR_SECUREWORD" => $lr_security_word,
            );
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Processors list";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Processor list</a> / Edit processor";

        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Processors list";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Processor list</a> / Edit processor";

        $id = $this->GetGP ("id");
        $code = $this->db->GetOne ("Select code From {$this->object} Where processor_id=$id");

        $name = $this->enc ($this->GetValidGP ("name", "Name", VALIDATE_NOT_EMPTY));
        $account_id = $this->enc ($this->GetValidGP ("account_id", "Account ID", VALIDATE_NOT_EMPTY));
        $routine_url = $this->enc ($this->GetValidGP ("routine_url", "Routine URL", VALIDATE_NOT_EMPTY));
        $fee = $this->GetValidGP ("fee", "Fee", VALIDATE_FLOAT_POSITIVE);

        $extra_field = $this->enc ($this->GetGP ("extra_field"));
        $strict_code = $this->enc ($this->GetGP ("strict_code"));

        $extraSQL = "";

        if ($code == "libertyreserve")
        {
            $extra_field = $this->db->GetOne ("Select extra_field From {$this->object} Where processor_id=$id");
            $libertyreserve_field = split ("\|\|", $extra_field);

            $lr_store = $this->enc ($this->GetGP ("lr_store"));
            $lr_security_word = $this->enc ($this->GetGP ("lr_security_word"));

            if ($lr_security_word == "********") $lr_security_word = base64_decode ($libertyreserve_field [1]);

            $extra_field = $lr_store."||".base64_encode ($lr_security_word);
            $extraSQL = ", extra_field='$extra_field'";
        }

        if ($code == "alertpay") $extraSQL = ", extra_field='".$extra_field."'";
        
        // $password = md5 ($this->GetGP ("password"));
        // $password_real = $this->db->GetSetting ("AdminAltPassword");
        // if ($password != $password_real) $this->SetError ("password", "Alternative Password is incorrect. Please try again");

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else {
            $this->db->ExecuteSql ("Update {$this->object} Set name='$name', account_id='$account_id', routine_url='$routine_url', fee='$fee' $extraSQL Where processor_id=$id");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id");
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=1-is_active Where processor_id=$id");

        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_
        <script language='JavaScript' src='../js/is_active.js'></script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("processors");

$zPage->Render ();

?>
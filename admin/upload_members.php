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
        $ec = $this->GetGP ("ec");
        $this->mainTemplate = "./templates/upload_members.tpl";
        $this->pageTitle = "Upload members";
        $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / Upload members";

        $file_err = "";
        if ($ec == "fl") $file_err = "<span class='error'>Error: The file has not been prepared properly.</span>";
        if ($ec == "emp") $file_err = "<span class='error'>Error: The file has not been selected.</span>";
        $file = "File: <input type='file' name='file_mem' value='' style='width: 320px;'>";

        $this->data = array (
            "MAIN_FILE" => $file,
            "MAIN_FILE_ERROR" => $file_err,
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            );
    }

    //------------------------------------------------------------------------------
    function ocd_fromfile ()
    {
        $file_mem = $this->enc ($this->GetValidGP ("file_mem", "Members file", VALIDATE_NOT_EMPTY));

        $cycling = $this->db->GetSetting ("cycling", 0);

        $key = 'file_mem';
        if ($this->errors['err_count'] > 1)
        {
            $this->Redirect ($this->pageUrl."?ec=emp");
        }
        else
        {
            $key = 'file_mem';
            if (array_key_exists ($key, $_FILES) and ($_FILES[$key]['error'] < 3))
            {
                $physical_path = $this->db->GetSetting ("PathSite");
                $tmp_name = $_FILES[$key]['tmp_name'];

                $types = $_FILES[$key]['type'];
                $types_array = explode("/", $types);
                if ( strpos($_FILES[$key]['name'],'php')!==false ) {
                    $this->SetError ($key, "Cannot upload this file.");
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($_FILES[$key]['name'], "csv");
                $whitelist = array("csv");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $filename = str_replace (" ", "_", $_FILES[$key]['name']);

                    if (file_exists ($physical_path."data/csv/".$filename))
                    {
                        $this->SetError ($key, "This file is already uploaded");
                        unlink ($physical_path."data/csv/".$filename);
                    }

                    if (!@move_uploaded_file ($tmp_name, $physical_path."data/csv/".$filename))
                    {
                        $this->SetError ($key, "Cannot upload this file.");
                    }
                    else
                    {
                        $cmd = "chmod 644 ".$physical_path."data/csv/".$filename;

                        exec ($cmd, $output, $retval);
                        $sep = ";";
                        $md = $physical_path."data/csv/".$filename;
                        $mem_list = fopen($md,"r") or die("Îøèáêà");
                        $first_line = fgets($mem_list, 4096);
                        $first_line_field = explode ($sep, $first_line);

                        $first_line_field[5] = trim($first_line_field[5]);
                        $num_str = 0;
                        $err = array();
                        while (!feof($mem_list))
                        {
                            $mem_str = trim(fgets($mem_list,4096));
                            if ($mem_str != "")
                            {
                                $mem_field = explode ($sep, $mem_str);
                                $error = false;
                                $fields = $values = "";
                                $enrollID = 0;
                                foreach ($first_line_field as $k => $v)
                                {
                                    $mem_field[$k] = str_replace (chr(160), "", $mem_field[$k]);

                                    $v = str_replace (chr(160), "", $v);

                                    switch ($v)
                                    {
                                        case "email":
                                            $count = $this->db->GetOne ("Select Count(*) From members Where email='{$mem_field[$k]}'");
                                            if ($count == 0)
                                            {
                                                $mem_field[$k] = str_replace (chr(160), "", $mem_field[$k]);
                                                if (preg_match ("/^[-_\.0-9a-z]+@[-_\.0-9a-z]+\.+[0-9a-z]{2,3}\$/i", trim($mem_field[$k])) == 0)
                                                {
                                                    $error = true;
                                                }
                                                $fields .= $v.", ";
                                                $values .= "'".trim($mem_field[$k])."',";
                                            }
                                            else
                                            {
                                                $error = true;
                                            }
                                        break;
                                        case "username":
                                            $count = $this->db->GetOne ("Select Count(*) From members Where username='{$mem_field[$k]}'", 0);
                                            if ($count == 0)
                                            {
                                                $fields .= $v.", ";
                                                $values .= "'".trim($mem_field[$k])."',";
                                            }
                                            else
                                            {
                                                $error = true;
                                            }
                                        break;
                                        case "passwd":
                                            $mem_field[$k] = md5 ($mem_field[$k]);
                                            $fields .= $v.", ";
                                            $values .= "'".trim($mem_field[$k])."',";
                                        break;
                                        case "enroller_id":
                                            if (is_numeric($mem_field[$k]) or $mem_field[$k] == "")
                                            {
                                                if ($mem_field[$k] == 0 or $mem_field[$k] == "")
                                                {
                                                    $mem_field[$k] = $this->db->GetOne ("Select member_id From `members` Order By RAND() Limit 1", 1);
                                                }
                                                $count = $this->db->GetOne ("Select Count(*) From `members` Where member_id='{$mem_field[$k]}'", 0);
                                                if ($count == 0)
                                                {
                                                    $error = true;
                                                }
                                                $fields .= $v.", ";
                                                $enrollID = trim($mem_field[$k]);
                                                $values .= trim($mem_field[$k]).",";
                                            }
                                            else
                                            {
                                                $error = true;
                                            }
                                        break;
                                        default :
                                            if ($v != "")
                                            {
                                                $fields .= $v.", ";
                                                $values .= "'".trim($mem_field[$k])."',";
                                            }
                                        break;
                                    }
                                }
                                $num_str += 1;

                                if ($fields != "" And !$error)
                                {
                                    $fields = substr ($fields, 0, -1);
                                    $values = substr ($values, 0, -1);
                                    $fields = $fields." reg_date, is_active";
                                    $values = ($cycling == 1)? $values.", ".time().", 1" :  $values.", ".time().", 0" ;
                                    $sql = "Insert into `members` (".$fields.") Values (".$values.")";

                                    $this->db->ExecuteSql ($sql);
                                }
                                else
                                {
                                    $err[] = $num_str;
                                }
                            }
                        }
                        $rt = Count ($err);
                    }
                    fclose($mem_list);
                    if (($filename!= "") and (file_exists ($physical_path."data/csv/".$filename))) unlink ($physical_path."data/csv/".$filename);
                }
                $all_err = implode(", ", $err);
                $this->mainTemplate = "./templates/file_details.tpl";
                $this->pageTitle = "Upload members";
                $this->pageHeader = "<a href='members.php' class='ptitle'>Members</a> / Upload members";
                $this->data = array (
                    "MAIN_HEADER" => $this->pageHeader,
                    "MAIN_ACTION" => "members.php",
                    "ALL_ROWS" => $num_str,
                    "ALL_SUCCESS" => $num_str - $rt,
                    "ALL_FAIL" => $rt,
                    "ALL_LIST_MISTAKE" => $all_err,
                    );
            }
            else
            {
                $this->Redirect ($this->pageUrl."?ec=emp");
            }
        }
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("members");

$zPage->Render ();

?>
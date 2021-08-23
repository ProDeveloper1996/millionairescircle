<?php

define ('C_DEFAULT', 1);
define ('C_RESULT', 2);
define ('C_ERROR', 3);
define ('LIMIT', 1);

//------------------------------------------------------------------------------

class Dumper
{
    var $db;
    var $path;

    //--------------------------------------------------------------------------
    function __construct ($database, $path)
    {
        $this->db = $database;
        $this->path = $path;
        if (!file_exists ($this->path)) {
            print $this->path;
            Exit ();
            mkdir ($this->path, 0777) || die ("Error: Cannot create folder for backup files.");
        }

        $this->SET['last_action'] = 0;
        $this->SET['last_db_backup'] = "";
        $this->SET['tables'] = "";
        $this->SET['comp_method'] = 2;
        $this->SET['comp_level']  = 7;
        $this->SET['last_db_restore'] = "";

        $this->tabs = 0;
        $this->records = 0;
        $this->size = 0;
        $this->comp = 0;
    }
    
    //--------------------------------------------------------------------------
    function backup ()
    {
        global $db;

        $tables = array ();
        $result = $db->ExecuteSql ("SHOW TABLES");
        $all = 0;
        while ($row = $db->FetchInArray($result))
        {
                $status = 0;
                if (!empty($tbls)) {
                    foreach($tbls AS $table){
                        $exclude = preg_match("/^\^/", $table) ? true : false;
                        if (!$exclude) {
                            if (preg_match("/^{$table}$/i", $row[0])) {
                                $status = 1;
                            }         
                            $all = 1;            
                        }
                        if ($exclude && preg_match("/{$table}$/i", $row[0])) {
                            $status = -1;
                        }
                    }
                }
                else {
                    $status = 1;
                }
                if ($status >= $all) {
                    $tables[] = $row[0];
                }
        }

        $tabs = count ($tables);
        $result = $db->ExecuteSql ("SHOW TABLE STATUS");
        $tabinfo = array ();
        $tabinfo[0] = 0;
        $info = '';
        while ($item = $db->FetchInAssoc($result))
        {
            if (in_array($item['Name'], $tables)) {
                $item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
                $tabinfo[0] += $item['Rows'];
                $tabinfo[$item['Name']] = $item['Rows'];
                $this->size += $item['Data_length'];
                $tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
                if($item['Rows']) $info .= "|" . $item['Rows'];
            }
        }

        $show = 10 + $tabinfo[0] / 50;
        $info = $tabinfo[0] . $info;
        $name = $this->db . '_' . date ("Y-m-d_H-i") . "";
        $fp = $this->fn_open ($name, "wb");

        $this->fn_write ($fp, "#RS|{$this->db}|{$tabs}|" . date("Y.m.d H:i:s") ."|{$info}\n\n");
        $t=0;

        $result = $db->ExecuteSql ("SET SQL_QUOTE_SHOW_CREATE = 1");
        foreach ($tables as $table)
        {
            header ("X-pmaPing: Pong");
            
            // Creating of table
            $result = $db->ExecuteSql ("SHOW CREATE TABLE {$table}");
            $tab = $db->FetchInArray($result);
            $tab = preg_replace ('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
            $this->fn_write($fp, "DROP TABLE IF EXISTS {$table};\n{$tab[1]};\n\n");

            $NumericColumn = array ();
            $result = $db->ExecuteSql ("SHOW COLUMNS FROM {$table}");
            $field = 0;
            while ($col = $db->FetchInArray($result)) {
                $NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
            }

            $fields = $field;
            $from = 0;
            $limit = $tabsize[$table];
            $limit2 = round($limit / 3);

            $i = 0;

            $insert = "";
            while(($result = $db->ExecuteSql ("SELECT * FROM {$table} LIMIT {$from}, {$limit}")) && ($total = $db->NumRows($result)))
            {
                while($row = $db->FetchInArray($result))
                {
                    $sql = [];
                    $i++;
                    $t++;
                    for($k = 0; $k < $fields; $k++)
                    {
                        if ($NumericColumn[$k])
                            $sql[$k] = isset($row[$k]) ? $row[$k] : "NULL";
                        else
                            $sql[$k] = isset($row[$k]) ? "'" . $db->Real($row[$k]) . "'" : "NULL";
                    }
                    $insert .= "INSERT INTO `{$table}` VALUES(" . implode(", ", $sql) . ");\n";
                }
                $db->FreeSqlResult($result);
                if ($total < $limit) break;
                $from += $limit;
            }
            if ($i != 0) $this->fn_write ($fp, $insert."\n\n");

            header ("X-pmaPing: Pong");
        }
        $this->tabs = $tabs;
        $this->records = $tabinfo[0];
        $this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];

        $this->fn_close ($fp);

        return $this->filename;
    }
    
    //--------------------------------------------------------------------------
    function restore ($file_install = "")
    {
        global $db;

        $file = "";
        if (isset ($_POST['file'])) $file = $_POST['file'];
        if ($file_install != "") $file = $file_install;
        if ($file == "") return "";

        header ("X-pmaPing: Pong");

        preg_match ("/^(\d+)\.(\d+)\.(\d+)/", mysqli_get_server_info ($db->dbConnect), $m);
        $this->mysql_version = sprintf ("%d%02d%02d", $m[1], $m[2], $m[3]);
        // Determine file format
        if (preg_match ("/^(.+?)\.sql(\.(bz2|gz))?$/", $file, $matches)) {
            $file = $matches[1];
        }

        $fp = $this->fn_open ($file, "rb");
        $this->file_cache = $sql = $table = $insert = "";
        $is_skd = $query_len = $execute = $q =$t = $i = $aff_rows = 0;
        $limit = 300;
        $index = 4;
        $tabs = 0;
        $cache = "";
        $info = array ();
        while (($str = $this->fn_read_str($fp)) !== false)
        {
            if (empty($str) || preg_match ("/^(#|--)/", $str))
            {
                if (!$is_skd and preg_match ("/^#RS\|/", $str)) {
                    $info = explode ("|", $str);
                    header ("X-pmaPing: Pong");
                    $is_skd = 1;
                }
                continue;
            }
            $query_len += strlen ($str);

            if (!$insert && preg_match ("/^(INSERT INTO `?([^` ]+)`? .*?VALUES)(.*)$/i", $str, $m))
            {
                if ($table != $m[2]) {
                    $table = $m[2];
                    $tabs++;
                    header ("X-pmaPing: Pong");
                    $i = 0;
                }
                $insert = $m[1] . ' ';
                $sql .= $m[3];
                $index++;
                $info[$index] = isset($info[$index]) ? $info[$index] : 0;
                $limit = round($info[$index] / 20);
                $limit = $limit < 300 ? 300 : $limit;
                if ($info[$index] > $limit){
                    $cache = '';
                }
            }
            else {
                $sql .= $str;
                if ($insert) {
                    $i++;
                    $t++;
                    if ($is_skd && $info[$index] > $limit && $t % $limit == 0){
                        header ("X-pmaPing: Pong");
                    }
                }
            }
            
            if (!$insert && preg_match ("/^CREATE TABLE (IF NOT EXISTS )?`?([^` ]+)`?/i", $str, $m) && $table != $m[2])
            {
                $table = $m[2];
                $insert = '';
                $tabs++;
                $i = 0;
            }

            if ($sql)
            {
                if (preg_match ("/;$/", $str)) {
                    $sql = rtrim($insert . $sql, ";");
                    if (empty($insert)) {
                        if ($this->mysql_version < 40101) {
                            $sql = preg_replace("/ENGINE\s?=/", "TYPE=", $sql);
                        }
                    }
                    $insert = '';
                    $execute = 1;
                }
                if ($query_len >= 65536 && preg_match("/,$/", $str)) {
                    $sql = rtrim($insert . $sql, ",");
                    $execute = 1;
                }

                if ($execute)
                {
                    $q++;

                    $db->ExecuteSql($sql) or trigger_error ("Incorrected SQL query .<BR>" . mysqli_error($db->dbConnect).'<br><br>SQL: '.$sql, E_USER_ERROR);
                    if (preg_match("/^insert/i", $sql)) {
                        $aff_rows += mysqli_affected_rows($db->dbConnect);
                    }
                    $sql = '';
                    $query_len = 0;
                    $execute = 0;
                }
            }
        }
        header ("X-pmaPing: Pong");

        $this->tabs = $tabs;
        $this->records = $aff_rows;
        $this->size = filesize ($this->path.$this->filename);
        $this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];

        $this->fn_close ($fp);

        return $this->filename;
    }

    //--------------------------------------------------------------------------
    function getRestoreFileSelect ()
    {
        $toRet = "<select name='file' style='width:340px;'>";
        foreach ($this->file_select () as $key => $value) {
            $toRet .= "<option value='{$key}'>{$value}";
        }
        $toRet .= "</select>";
        return $toRet;
    }
    
    //--------------------------------------------------------------------------
    function file_select ()
    {
        $files = array ("" => "Select the file");
        if (is_dir ($this->path) && $handle = opendir ($this->path))
        {
            while (false !== ($file = readdir ($handle)))
            {
                if (preg_match ("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {
                    $filesize = Round (filesize ($this->path.$file) / 1024);
                    $files[$file] = $file." ($filesize Kb)";
                } 
            }
            closedir ($handle);
        }
        return $files;
    }
    
    //--------------------------------------------------------------------------
    function fn_open ($name, $mode)
    {
        $this->filename = "{$name}.sql.gz";
        return gzopen($this->path.$this->filename, "{$mode}b7");
    }
    
    //--------------------------------------------------------------------------
    function fn_write ($fp, $str)
    {
        gzwrite ($fp, $str);
    }
    
    //--------------------------------------------------------------------------
    function fn_read ($fp)
    {
        return gzread ($fp, 4096);
    }
    
    //--------------------------------------------------------------------------
    function fn_read_str ($fp)
    {
        $string = '';
        $this->file_cache = ltrim($this->file_cache);
        $pos = strpos($this->file_cache, "\n", 0);
        if ($pos < 1) {
            while (!$string && ($str = $this->fn_read($fp))){
                $pos = strpos($str, "\n", 0);
                if ($pos === false) {
                    $this->file_cache .= $str;
                }
                else{
                    $string = $this->file_cache . substr($str, 0, $pos);
                    $this->file_cache = substr($str, $pos + 1);
                }
            }
            if (!$str) {
                if ($this->file_cache) {
                    $string = $this->file_cache;
                    $this->file_cache = '';
                    return trim($string);
                }
                return false;
            }  
        }
        else {
              $string = substr($this->file_cache, 0, $pos);
              $this->file_cache = substr($this->file_cache, $pos + 1);
        }
        return trim($string);
    }
    
    //--------------------------------------------------------------------------
    function fn_close ($fp)
    {
        gzclose ($fp);
        set_chmod ($this->path.$this->filename);
    }
    
}

//------------------------------------------------------------------------------

function set_chmod ($file)
{
    if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
        @chmod ($file, 0666);
    }
    else {
        $cmd = "chmod 666 ".$file;
        exec ($cmd, $output, $retval);
    }
}

?>
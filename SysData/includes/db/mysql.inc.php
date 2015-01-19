<?php
class db_mysql
{
    function __construct($host, $username, $password, $database = 'travlr')
    {
        $this->db = new mysqli($host, $username, $password, $database);
    }
    
    function __deconstruct()
    {
        unset($this->db);
    }
    
    public function query($sql)
    {
        $args = func_get_args();
        for($i=1;$i<=func_num_args()-1;$i++)
        {
            $sql = str_replace(':'.$i, '**sqlsafeplaceholder'.$i.'**', $sql);
        }
        for($i=1;$i<=func_num_args()-1;$i++)
        {
            $sql = str_replace('**sqlsafeplaceholder'.$i.'**', $this->db->real_escape_string($args[$i]), $sql);
        }
        $result = $this->db->query($sql);
        if($result === false)
        {
        	$trace = debug_backtrace();
        	echo 'query error: <strong>'.$this->db->error.'</strong> in '.$trace[0]['file'].' on line '.$trace[0]['line'];
        	die();
        }
        $rows = array();

        if($result && is_object($result) && $result->num_rows > 0) {
			while($currRow = $result->fetch_assoc())
			{
				$rows[] = $currRow;
			}
		}
        return $rows;
    }
    public function writeQuery($sql)
    {
        $args = func_get_args();
        for($i=1;$i<=func_num_args()-1;$i++)
        {
            $sql = str_replace(':'.$i, '**sqlsafeplaceholder'.$i.'**', $sql);
        }
        for($i=1;$i<=func_num_args()-1;$i++)
        {
            $sql = str_replace('**sqlsafeplaceholder'.$i.'**', $this->db->real_escape_string($args[$i]), $sql);
        }
        
        if($this->db->query($sql) === false)
        {
        	$trace = debug_backtrace();
        	echo 'query error: <strong>'.$this->db->error.'</strong> in '.$trace[0]['file'].' on line '.$trace[0]['line'];
        	die();
        }
    }
    public function lastRowID()
    {
        return $this->db->insert_id;
    }
    public function debugQuery($sql)
    {
		$args = func_get_args();
        for($i=1;$i<=func_num_args()-1;$i++)
        {
            $sql = str_replace(':'.$i, $this->db->real_escape_string($args[$i]), $sql);
        }
        echo $sql;
        return array();
    }
}
?>

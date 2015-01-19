<?php
class db_sqlite
{
    function __construct($dbFile)
    {
        $this->db = new SQLite3($dbFile);
        $this->db->busyTimeout('10000');
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
            $sql = str_replace(':'.$i, $this->db->escapeString($args[$i]), $sql);
        }
        $result = $this->db->query($sql);
        $rows = array();
        while($currRow = $result->fetchArray())
        {
            $rows[] = $currRow;
        }
        return $rows;
    }
    public function writeQuery($sql)
    {
        $args = func_get_args();
        for($i=1;$i<=func_num_args()-1;$i++)
        {
//        	$safe = $args[$i];
        		//$safe = str_replace('\'', '\'\'',$args[$i]);
			$safe = sqlite3::escapeString($args[$i]);
            $sql = str_replace(':'.$i, $safe, $sql);
        }
        $this->db->query($sql);
    }
    public function lastRowID()
    {
        return $this->db->lastInsertRowID();
    }
    public function debugQuery($sql)
    {
	$args = func_get_args();
        for($i=1;$i<=func_num_args()-1;$i++)
        {
        	$safe = sqlite3::escapeString($args[$i]);
            $sql = str_replace(':'.$i, $safe, $sql);
        }
	echo $sql;
        return array();
    }
}
?>

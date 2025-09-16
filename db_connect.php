<?php
include'db.php';
class db_connect{
	protected $db;
    protected function __construct($db = NULL)
    {
        if (is_object($db)) {
            $this->db = $db;
        }  else  {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            try  {
                $this->db = new PDO($dsn, DB_USER, DB_PSWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
            } catch (Exception $e) {
                die ($e->getMessage());
            }
        }
    }
}
?>
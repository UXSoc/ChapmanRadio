<?php namespace ChapmanRadio;
use \PDO;

class DB extends \Sinopia\DB
{

    public static function Init($host,$dbname,$user,$password)
    {
        try {
            self::Setup(new PDO("mysql:host=" . $host . ";dbname=". $dbname, $user, $password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_FOUND_ROWS => FALSE)));
        } catch (\PDOException $e) {
            echo "Unable to connect to database! Please try again soon!<br />\n";
            echo 'Connection failed: ' . $e->getMessage() . '<br />' . "\n";
            if (!isset(self::$link) || self::$link == null) exit;
        }
    }

    public static function Update($table, $idcolumn, $idvalue, $data)
    {
        $fields = array();
        $d = array();
        foreach ($data as $field => $value) {
            $fields[] = "`$field` = :$field";
            $d[":$field"] = $value;
        }
        $d[":_idvalue"] = $idvalue;
        return self::Query("UPDATE `$table` SET " . implode(',', $fields) . " WHERE `$idcolumn` = :_idvalue", $d);
    }

}

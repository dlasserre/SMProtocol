<?php
/**
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * Class mysql
 */
class mysql extends PDO
{
    /** @var  mysql $_instance */
    protected static $_instance;

    public function __construct()
    {
        parent::__construct('mysql:dbname=download;host=127.0.0.1', 'root', 'XnyexbUF');
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return mysql
     */
    public static function getInstance()
    {
        if(!self::$_instance instanceof mysql) {
            self::$_instance = new mysql();
        }
        /** Return */
        return (self::$_instance);
    }

} 
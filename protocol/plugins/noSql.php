<?php
/**
 * Class noSql
 */
class noSql
{
    protected static $_instance;
    protected $_mongodb;

    private function __construct($environment)
    {
        try {
            \library\SMProtocol\SMProtocol::_print('[plugin:noSql] ' . COLOR_BLUE . 'Configuration loaded on ' .
                $environment['host'] . ' port=' . $environment['port'] . ', db=' . $environment['db'] . COLOR_WHITE . PHP_EOL);
            $this->_mongodb = new MongoClient('mongodb://' . $environment['host'] . ':' . $environment['port'], array('connect' => True));
            \library\SMProtocol\SMProtocol::_print('[plugin:noSql] ' . COLOR_GREEN . 'MongoDb connection established' . COLOR_WHITE . PHP_EOL);
        } catch (MongoConnectionException $e) {
            \library\SMProtocol\SMProtocol::_print('[plugin:noSql] '.COLOR_RED.$e->getMessage().COLOR_WHITE.PHP_EOL);
            exit;
        }
    }

    public static function getInstance($environment = null)
    {
        if(!self::$_instance instanceof noSql)
            self::$_instance = new noSql($environment);
        return ( self::$_instance->_mongodb );
    }
} 
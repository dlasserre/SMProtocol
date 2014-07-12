<?php
/** Namespace engine */
namespace engine;
use engine\exception\client;
use engine\exception\server;
use engine\exception\socket;
use protocol\definition;

/**
 * Class SMProtocol
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine
 */
class SMProtocol
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        /** @var resource $_dir */
        $_dir = opendir(APPLICATION_PATH.'/protocol/');

        /** @var string $directory */
        while($directory = readdir($_dir))
        {
            if(is_dir(APPLICATION_PATH.'/protocol/'.$directory) and !in_array($directory, array('interfaces', '..', '.')))
            {
                /** @var string $file */
                $file = APPLICATION_PATH.'/protocol/'.$directory.'/interpret.php';

                if(file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    require_once($file);
                    /** @var string $_class */
                    $_class = '\protocol\\'.$directory.'\interpret';
                    try {
                        /** @var definition $_instance */
                        $_instance = new $_class();
                        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                        new \engine\server\server($_instance);
                    } catch(server $server) {
                        /** Catch server exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($server->getMessage());
                        else echo $server->getMessage();
                    } catch(client $client) {
                        /** Catch client exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($client->getMessage());
                        else echo $client->getMessage();
                    }catch(socket $socket ) {
                        /** Catch socket exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($socket->getMessage());
                        else echo $socket->getMessage();
                    }
                }
            }
        }
    }
} 
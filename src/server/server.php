<?php
/** Namespace engine / server */
namespace engine\server;
use engine\exception\client;
use protocol\definition;
use protocol\smtp\interpret;

/**
 * Class server
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class server extends initialize
{
    /** @var  bool $__server_listening */
    public static $__server_listening;
    /** @var  array $_clients */
    public static $_clients;
    /** @var array array */
    protected $_streams;

    /**
     * @param definition | interpret $definition
     * @throws server
     * @throws client
     */
    public function __construct(definition $definition)
    {
        /** function to closing socket if signal received */
        pcntl_signal(SIGINT, array('\engine\server\signal', 'handler'));
        pcntl_signal(SIGCHLD, array('\engine\server\signal', 'handler'));
        pcntl_signal(SIGTERM, array('\engine\server\signal', 'handler'));

        if(false !== parent::__construct($definition)) {
            /** Launch wait connection */
            do {
                /** @var array $_streams */
                $this->_streams = array(parent::$_socket);
                $_streams = $this->_streams;

                /** @var bool __server_listening */
                self::$__server_listening = true;

                if(@socket_select($_streams, $array = null, $expect = null, null)) {
                    /** @var int $i */
                    for($i = 0; $i < count($_streams); $i++) {
                        /** @var resource $_client */
                        $_client = socket_accept(self::$_socket);
                        echo 'accepted'.PHP_EOL;
                        if($_client > 0) {
                            /** @var int $_pid */
                            $_pid = pcntl_fork();
                            if($_pid < 0) {
                                throw new client(pcntl_get_last_error());
                            } else if ($_pid) {
                                self::$_clients[$_pid] = $_client;
                            } else {
                                $definition->transmission(new socket($_client));
                            }
                        }
                    }
                }
            } while(self::$__server_listening);
        }
    }
}
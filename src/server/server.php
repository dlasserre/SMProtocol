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
    /** @var  string $_name */
    protected $_name;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param definition | interpret $definition
     * @param string $_name
     * @throws \engine\exception\server
     * @throws \engine\exception\client
     */
    public function __construct(definition $definition, $_name)
    {
        /** function to closing socket if signal received */
        pcntl_signal(SIGINT, array('\engine\server\signal', 'handler'));
        pcntl_signal(SIGCHLD, array('\engine\server\signal', 'handler'));
        pcntl_signal(SIGTERM, array('\engine\server\signal', 'handler'));

        /** restarting signal */
        pcntl_signal(SIGHUP, array($this, 'restart'));

        if(false !== parent::__construct($definition)) {
            /** protocol name */
            $this->_name = $_name;
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
                                if(posix_setsid() < 0) {
                                    throw new \engine\exception\server('Impossible to make the current process a session leader.');
                                }
                                self::$_clients[$_pid] = $_client;
                            } else { // Child process
                                $definition->transmission(new socket($_client));
                            }
                        }
                    }
                }
            } while(self::$__server_listening);
        }
    }

    public function restart()
    {
        echo 'restarting '.$this->_name.'...'.PHP_EOL;
    }
}
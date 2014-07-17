<?php
/** Namespace engine / server */
namespace engine\server;
use protocol\definition;
use protocol\interfaces\hook;

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
     * @param definition $definition
     * @param string $_name
     * @throws \engine\exception\server
     * @throws \engine\exception\client
     */
    public function __construct(definition $definition, $_name)
    {
        pcntl_signal(SIGINT, array($this, 'kill'));

        if(false !== parent::__construct($definition, $_name)) {
            /** protocol name */
            $this->_name = $_name;
            /** @var array $_streams */
            $this->_streams = array(parent::$_socket);
            /** @var array $_hooks */
            $_hooks = array();

            for(;;)
            {
                /** @var array $_streams */
                $_streams = array_merge((array)self::$_clients, $this->_streams);

                if(@socket_select($_streams, $_write = array(), $expect = null, null) < 1) {
                    continue;
                }

                foreach($_streams as $socket) {
                    if(in_array(parent::$_socket, $_streams)) {
                        /** New connection */
                        if($socket === parent::$_socket) {
                            /** @var resource $_client */
                            $_client = socket_accept(parent::$_socket);

                            if($_client > 0) {
                                self::$_clients[] = $_client;
                                /** @var string $_hook_class */
                                $_hook_class = $this->_name.'\hook';
                                /** @var hook $_hook */
                                $_hook = new $_hook_class(new sender($_client, $definition));
                                socket_getpeername($_client, $address, $port);
                                /** Call preDispatching hook method */
                                $_hook->preDispatch($address, $port);
                                $_hooks[(string)$_client] = $_hook;

                                if($_client <= 0) { /** Exception */
                                    throw new \engine\exception\server(socket_last_error($_client));
                                }
                            } else /** Exception */
                                throw new \engine\exception\server(socket_last_error($_client));
                        }
                    } else {
                        /** @var \hook[] $_hooks */
                        if(!$_hooks[(string)$socket]->isClosed()) {
                            /** @var string $_data */
                            $_data = socket_read($socket, (int)$definition->block_size);

                            if($_data) {
                                /** @var \hook[] $_hooks */
                                $_hooks[(string)$socket]->dispatch($_data);
                                if($_hooks[(string)$socket]->isClosed()) {
                                    /** Call postDispatching hook method */
                                    $_hooks[(string)$socket]->postDispatch(null);
                                    /** remove from client stack */
                                    unset(self::$_clients[(string)$socket]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function restart($sig)
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1);
        /** Close socket */
        parent::close();
        exit($sig);
    }
}
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
     * @param definition | interpret $definition
     * @param string $_name
     * @param hook $_hook
     * @throws \engine\exception\server
     * @throws \engine\exception\client
     */
    public function __construct(definition $definition, $_name, hook $_hook = null)
    {
        if(false !== parent::__construct($definition, $_name)) {
            /** protocol name */
            $this->_name = $_name;
            /** @var array $_streams */
            $this->_streams = array(parent::$_socket);

            for(;;) {
                /** @var array $_streams */
                $_streams = array_merge((array)self::$_clients, $this->_streams);

                if(@socket_select($_streams, $array = null, $expect = null, null) > 0) {
                    if(in_array(parent::$_socket, $_streams)) {
                        /** @var resource $_client */
                        $_client = socket_accept(parent::$_socket);
                        if($_client > 0) {
                            self::$_clients[] = $_client;
                            if($_client > 0) {
                                $definition->transmission(new socket($_client, $_hook));
                            }
                        } else /** Exception */
                            throw new \engine\exception\server(socket_last_error($_client));
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
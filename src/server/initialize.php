<?php
/** Namespace engine\server */
namespace engine\server;
use engine\exception\server;
use protocol\definition;

/**
 * Class initialize
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class initialize
{
    /** @var  definition $_definition */
    protected $_definition;
    /** @var  resource $_socket */
    protected static $_socket;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param definition $definition
     * @throws server
     */
    public function __construct(definition $definition)
    {
        if($definition->host and $definition->port and $definition->port) {
            /** @var definition _definition */
            $this->_definition = $definition;
            if($this->_initSocket())
                /** Return */
                return (true);
        } else throw new server();
        /** Return */
        return (false);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @throws \engine\exception\server
     */
    private function _initSocket()
    {
        /** @var resource $_socket */
        $_socket = socket_create($this->_definition->socket_domain,
            $this->_definition->socket_type, $this->_definition->socket_protocol);

        if($_socket === false) {
            echo socket_last_error();
            throw new server(socket_last_error());
        }
        /** Binding socket on host and port. */
        do {
            /** @var bool $binding */
            $binding = @socket_bind($_socket, $this->_definition->host, $this->_definition->port);
        } while($binding <= 0);
        /** Listen socket */
        if(socket_listen($_socket) <= 0) {
            echo socket_last_error();
            throw new server(socket_last_error($_socket));
        }
        self::$_socket = $_socket;
        /** Return */
        return (true);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public static function close()
    {
        if(gettype(self::$_socket) == 'resource')
            socket_close(self::$_socket);
    }
} 
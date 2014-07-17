<?php
/** Namespace engine\server */
namespace engine\server;
use engine\exception\server;
use engine\SMProtocol;
use src\abstracts\definition;

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
    /** @var  string $_name */
    protected $_name;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param \src\abstracts\definition $definition
     * @param $_name
     * @throws \engine\exception\server
     * @internal param string $name
     */
    public function __construct(\src\abstracts\definition $definition, $_name)
    {
        if($definition->host and $definition->port) {
            $this->_name = $_name;
            /** @var \src\abstracts\definition _definition */
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
            throw new server(socket_last_error());
        }
        /** Binding socket on host and port. */
        $dot = '['.$this->_name.'] Binding on '.COLOR_BLUE.$this->_definition->host.':'.$this->_definition->port.COLOR_WHITE;
        do {
            $dot .= '.';
            /** @var bool $binding */
            $binding = socket_bind($_socket, $this->_definition->host, $this->_definition->port);
            sleep(2);
            SMProtocol::_print($dot."\r");
        } while($binding <= 0);
        SMProtocol::_print(COLOR_WHITE.PHP_EOL);
        /** Listen socket */
        if(socket_listen($_socket) <= 0) {
            throw new server(socket_last_error($_socket));
        }
        SMProtocol::_print('['.$this->_name.'] '.COLOR_GREEN.'Running success'.COLOR_WHITE.PHP_EOL);
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
<?php
/** Namespace engine\server */
namespace library\SMProtocol\server;
use library\SMProtocol\cleanup;
use library\SMProtocol\engine\server\semaphore;
use library\SMProtocol\exception;
use library\SMProtocol\SMProtocol;
use library\SMProtocol\interfaces\definition;

/**
 * Class initialize
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class initialize extends cleanup
{
    /** @var  definition $_definition */
    protected $_definition;
    /** @var  resource $_socket */
    protected static $_socket;
    /** @var  string $_name */
    protected $_name;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param \library\SMProtocol\abstracts\definition|\library\SMProtocol\interfaces\definition $definition
     * @param string $_name
     * @throws \library\SMProtocol\exception\server
     * @internal param string $name
     */
    public function __construct(\library\SMProtocol\abstracts\definition $definition, $_name)
    {
        if($definition->host and $definition->port) {
            $this->_name = $_name;
            /** @var \library\SMProtocol\abstracts\definition _definition */
            $this->_definition = $definition;
            if($this->_initSocket())
                /** Return */
                return (true);
        } else throw new exception\server();
        /** Return */
        return (false);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @throws exception\server
     */
    private function _initSocket()
    {
        /** @var resource $_socket */
        $_socket = socket_create($this->_definition->socket_domain,
            $this->_definition->socket_type, $this->_definition->socket_protocol);

        if(false === $_socket) {
            throw new exception\server(socket_last_error());
        }
        /** @var string $dot */
        /** Binding socket on host and port. */
        $dot = '['.$this->_name.'] Binding on '.COLOR_BLUE.$this->_definition->host.':'.$this->_definition->port.COLOR_WHITE;
        do {
            $dot .= '.';
            /** @var bool $binding */
            $binding = @socket_bind($_socket, $this->_definition->host, $this->_definition->port);
            sleep(2);
            SMProtocol::_print($dot."\r");
            if(socket_last_error($_socket)) {
                continue;
            }
        } while($binding <= 0);
        SMProtocol::_print(COLOR_WHITE.PHP_EOL);
        if(!$this->_definition->sumaxconn) {
            $this->_definition->sumaxconn = SOMAXCONN;
        }
        SMProtocol::_print('['.COLOR_RED.'Kernel:SOMAXCONN'.COLOR_WHITE.'] Set at '.$this->_definition->sumaxconn.', by default the system was set at '.SOMAXCONN.' (Max simultaneously sockets manage by the kernel)'.PHP_EOL);
        /** Listen socket */
        if(socket_listen($_socket, $this->_definition->sumaxconn) <= 0) {
            throw new exception\server(socket_last_error($_socket));
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

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param null $class
     */
    public function _cleanup($class = null)
    {
        parent::_cleanup($class);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     */
    public function __destruct()
    {
        parent::_cleanup(__CLASS__);
    }
} 
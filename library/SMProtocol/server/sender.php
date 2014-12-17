<?php
/** Namespace */
namespace library\SMProtocol\engine\server;
/** Use */
use library\SMProtocol\exception\socket;
use library\SMProtocol\SMProtocol;
use library\SMProtocol\abstracts\definition;

/**
 * Class sender
 * @author Damien Lasserre <dlasserre@talend.com>
 * @package engine\server
 */
class sender
{
    /** @var string $_protocol */
    protected $_protocol;
    /** @var  string $address */
    public $address;
    /** @var  int $port */
    public $port;

    /** @var resource $_socket */
    protected $_socket;

    /** @var  bool $close */
    public $close;
    /** @var  int $_last_error_code */
    public $_last_error_code;

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param resource $_socket
     * @param string $_protocol
     * @param $definition $definition
     */
    public function __construct($_socket, definition $definition, $_protocol)
    {
        socket_getpeername($_socket, $this->address, $this->port);
        $this->close = False;
        if(is_resource($_socket)) {
            $this->_socket = $_socket;
            $this->_protocol = $_protocol;
        }
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param string $data
     * @param string $debug
     * @throws \library\SMProtocol\exception\socket
     * @return bool
     */
    public function send($data, $debug = null)
    {
        /** @var int $_length */
        $_length = mb_strlen($data);

        if($_length !== false) {
            if (@socket_write($this->_socket, $data, $_length) <= 0) {
                $this->_last_error_code = socket_last_error($this->_socket);
                /** Return */
                return (false);
            }
            SMProtocol::_print('['.$this->_protocol.']'.COLOR_GREEN.' >>> '.strlen($data).
                ' bytes to <'.$this->address.':'.$this->port.COLOR_BLUE.'@pid:'.posix_getpid().COLOR_GREEN.'>: '.COLOR_ORANGE.$debug.COLOR_WHITE.PHP_EOL);
        }

        /** Return */
        return (True);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param int $block_size
     * @return bool|string
     */
    public function received($block_size = 512)
    {
        /** @var string $_data */
        $_data = socket_read($this->_socket, (int)$block_size);
        if(empty($_data)) return ( False );
        if($_data) {
            SMProtocol::_print('['.$this->_protocol.']'.COLOR_ORANGE.' <<< '.strlen($_data).' bytes from <'.$this->address.':'.$this->port.'>'.COLOR_WHITE.PHP_EOL);
            /** Return */
            return ( $_data );
        }
        /** Return */
        return ( false );
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return resource
     */
    public function getSocket()
    {
        /** Return */
        return ($this->_socket);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     */
    public function close()
    {
        $this->close = True;
    }
}
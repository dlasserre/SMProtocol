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
    protected $_protocol;
    public $address;
    public $port;

    /** @var resource $_socket */
    protected $_socket;

    /** @var  bool $close */
    public $close;

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
     * @throws \library\SMProtocol\exception\socket
     * @return bool
     */
    public function send($data)
    {
        /** @var int $_length */
        $_length = mb_strlen($data);

        if($_length !== false) {
            if (socket_write($this->_socket, $data, $_length) <= 0) {
                throw new socket(socket_last_error($this->_socket));
            }
        }
        SMProtocol::_print('['.$this->_protocol.']'.COLOR_GREEN.' >>> '.strlen($data).' bytes to <'.$this->address.':'.$this->port.'>'.COLOR_WHITE.PHP_EOL);
        /** Return */
        return (True);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     */
    public function close()
    {
        if(is_resource($this->_socket)) {
            socket_close($this->_socket);
        }
        $this->close = True;
    }
}
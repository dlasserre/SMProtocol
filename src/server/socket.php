<?php
/** Namespace */
namespace engine\server;

/**
 * Class socket
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class socket
{
    /** @var resource $_socket */
    private $_socket;
    /** @var int $_buffer */
    private $_buffer = 128;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param resource $socket
     * @throws \engine\exception\socket
     * @description just sent data
     */
    public function __construct($socket)
    {
        if(is_resource($socket)) {
            $this->_socket = $socket;
        } else throw new \engine\exception\socket();
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $data
     * @return void
     * @throws \engine\exception\socket
     */
    public function ping($data)
    {
        if(socket_write($this->_socket, $data, strlen($data)) <= 0)
            throw new \engine\exception\socket(socket_last_error($this->_socket));
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $data
     * @return string
     *
     * @description sent data and wait to received data from client
     */
    public function pingPong($data)
    {
        $this->ping($data);
        /** @var string $_data */
        $_data = socket_read($this->_socket, $this->_buffer);
        return ($_data);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return string
     * @description just received data
     */
    public function pong()
    {
        /** @var string $_data */
        $_data = socket_read($this->_socket, $this->_buffer);
        return ($_data);
    }
} 
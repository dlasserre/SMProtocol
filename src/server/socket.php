<?php
/** Namespace */
namespace engine\server;
use protocol\interfaces\hook;

/**
 * Class socket
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class socket
{
    /** @var resource $_socket */
    private $_socket;
    /** @var  string $_address */
    protected $_address;
    /** @var  int $_port */
    protected $_port;
    /** @var \protocol\interfaces\hook $_hook */
    protected $_hook;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param resource $socket
     * @param hook $_hook
     * @throws \engine\exception\socket
     * @description just sent data
     */
    public function __construct(&$socket, hook $_hook = null)
    {
        if(null !== $_hook)
            $this->_hook = $_hook;
        if(is_resource($socket)) {
            $this->_socket = $socket;
        } else throw new \engine\exception\socket();
        socket_getpeername($this->_socket, $this->_address, $this->_port);
        (($this->_hook())?$this->_hook->preDispatch($this->_address, $this->_port):null);
        echo '['.$this->_address.':'.$this->_port.'] Connected ['.posix_getpid().']'.PHP_EOL;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $data
     * @return bool
     * @throws \engine\exception\socket
     */
    public function ping($data)
    {
        (($this->_hook())?$this->_hook->dispatching($data):null);
        if($this->push($data)) {
            /** Return */
            return (True);
        }
        /** Return */
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return bool
     */
    protected function _hook()
    {
        if($this->_hook instanceof hook)
            return (True);
        /** Return */
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $data
     * @param int $_length
     * @throws \engine\exception\socket
     * @return string
     * @description sent data and wait to received data from client
     */
    public function pingPong($data, $_length = 128)
    {
        (($this->_hook())?$this->_hook->dispatching($data):null);
        if($this->ping($data)) {
            if($_data = $this->pull($_length)) {
                (($this->_hook())?$this->_hook->postDispatch($_data):null);
                /** Return */
                return ($_data);
            }
        }
        (($this->_hook())?$this->_hook->postDispatch(null):null);
        /** Return */
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $_length
     * @throws \engine\exception\socket
     * @return string
     * @description just received data
     */
    public function pong($_length = 128)
    {
        /** @var string $_data */
        if($_data = $this->pull($_length)) {
            (($this->_hook())?$this->_hook->postDispatch($_data):null);
            /** Return */
            return ($_data);
        }
        (($this->_hook())?$this->_hook->dispatching(null):null);
        /** Return */
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $_buffer
     * @return bool
     */
    protected function pull($_buffer = 128)
    {
        /** @var string $_data */
        $_data = @socket_read($this->_socket, $_buffer);
        (($this->_hook())?$this->_hook->dispatching($_data):null);
        if($_data) {
            (($this->_hook())?$this->_hook->postDispatch($_data):null);
            /** Return */
            return ($_data);
        } else {
            /** Socket closed */
            if(socket_last_error($this->_socket) == SOCKET_ECONNRESET) {
                /** Return */
                return (False);
            }
        }
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $_data
     * @return bool
     * @throws \engine\exception\socket
     */
    protected function push($_data)
    {
        if(@socket_write($this->_socket, $_data, sizeof($_data)))
            /** Return */
            return (True);
        /** Return */
        return (False);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function _destruct()
    {
        echo '['.$this->_address.':'.$this->_port.'] Connection closing ['.posix_getpid().']'.PHP_EOL;
        foreach(server::$_clients as $i => $client) {
            if($client == $this->_socket) {
                echo 'socket remove from list'.PHP_EOL;
                unset(server::$_clients[$i]);
            }
        }
        socket_close($this->_socket);
    }
}
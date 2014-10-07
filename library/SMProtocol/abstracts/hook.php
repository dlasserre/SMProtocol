<?php
/** Namespace */
namespace library\SMProtocol\abstracts;
/** Usage */
use library\SMProtocol\engine\server\sender;
use library\SMProtocol\cleanup;
/**
 * Class hook
 */
abstract class hook extends cleanup implements \library\SMProtocol\interfaces\hook
{
    /** @var \library\SMProtocol\engine\server\sender  $_sender */
    private $_sender;

    public function __construct(sender $sender = null)
    {
        $this->_sender = $sender;
    }

    public function send($data, $debug = null)
    {
        return ($this->_sender->send($data, $debug));
    }

    public function getSocket()
    {
        return ($this->_sender->getSocket());
    }

    public function getAddress()
    {
        return ($this->_sender->address);
    }

    public function getPort()
    {
        return ($this->_sender->port);
    }

    public function received()
    {
        return ($this->_sender->received());
    }

    public function close()
    {
        $this->_sender->close();
    }

    public function isClosed()
    {
        return ($this->_sender->close);
    }

    public function getErrorMessage($code)
    {
        return ( socket_strerror($code) );
    }

    public function getLastErrorCode()
    {
        return ($this->_sender->_last_error_code);
    }

    public function __cleanup($_class)
    {
        parent::_cleanup($_class);
    }
} 
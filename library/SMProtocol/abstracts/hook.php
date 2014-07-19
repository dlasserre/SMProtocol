<?php
/** Namespace */
namespace library\SMProtocol\abstracts;
/** Usage */
use library\SMProtocol\engine\server\sender;
/**
 * Class hook
 */
abstract class hook implements \library\SMProtocol\interfaces\hook
{
    private $_sender;

    public function __construct(sender $sender = null)
    {
        $this->_sender = $sender;
    }

    public function send($data)
    {
        return ($this->_sender->send($data));
    }

    public function getAddress()
    {
        return ($this->_sender->address);
    }

    public function getPort()
    {
        return ($this->_sender->port);
    }

    public function close()
    {
        $this->_sender->close();
    }

    public function isClosed()
    {
        return ($this->_sender->close);
    }
} 
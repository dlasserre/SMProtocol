<?php
/** Namespace */
namespace library\SMProtocol\abstracts;

/** Usage */
use library\SMProtocol\engine\server\sender;
use library\SMProtocol\cleanup;

/**
 * Class hook
 * @author Damien Lasserre <dlasserre@talend.com>
 */
abstract class hook extends cleanup implements \library\SMProtocol\interfaces\hook
{
    /** @var \library\SMProtocol\engine\server\sender  $_sender */
    private $_sender;

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param sender $sender
     */
    public function __construct(sender $sender = null)
    {
        $this->_sender = $sender;
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param mixed $data
     * @param null $debug
     * @return bool
     */
    public function send($data, $debug = null)
    {
        /** Return */
        return ($this->_sender->send($data, $debug));
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return resource
     */
    public function getSocket()
    {
        /** Return */
        return ($this->_sender->getSocket());
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return string
     */
    public function getAddress()
    {
        /** Return */
        return ($this->_sender->address);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return int
     */
    public function getPort()
    {
        /** Return */
        return ($this->_sender->port);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param int $size
     * @return bool|string
     */
    public function received($size = 512)
    {
        /** Return */
        return ($this->_sender->received($size));
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     */
    public function close()
    {
        $this->_sender->close();
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return bool
     */
    public function isClosed()
    {
        /** Return */
        return ($this->_sender->close);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param string $code
     * @return string
     */
    public function getErrorMessage($code)
    {
        /** Return */
        return ( socket_strerror($code) );
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @return int
     */
    public function getLastErrorCode()
    {
        /** Return */
        return ($this->_sender->_last_error_code);
    }

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param $_class
     */
    public function __cleanup($_class)
    {
        parent::_cleanup($_class);
    }
} 
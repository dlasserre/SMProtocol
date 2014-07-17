<?php
/**
 * Class hook
 */
abstract class hook implements src\interfaces\hook
{
    private $_sender;

    public function __construct(\engine\server\sender $sender = null)
    {
        $this->_sender = $sender;
    }

    public function send($data)
    {
        return ($this->_sender->send($data));
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
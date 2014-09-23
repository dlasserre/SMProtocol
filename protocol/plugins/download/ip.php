<?php
/**
 * Class ip
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class ip extends \library\SMProtocol\cleanup
{
    /** @var  string $ip */
    public $ip;
    /** @var  int $port */
    public $port;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $ip
     */
    public function __construct($ip = null, $port = null)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $class
     */
    public function _cleanup($class = null)
    {
        if(null !== $class)
            parent::_cleanup($class);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __destruct()
    {
        parent::_cleanup(__CLASS__);
    }
} 
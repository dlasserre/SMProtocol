<?php
/**
 * Class ip
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class ip
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
} 
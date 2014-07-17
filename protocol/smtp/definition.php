<?php
/** Namespace protocol\smtp */
namespace protocol\smtp;

/**
 * Class interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\smtp
 */
class definition extends \src\abstracts\definition
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 4242;

        $this->forward_host = '127.0.0.1';
        $this->forward_port = 4242;
    }

    public function exception(\Exception $exception)
    {
        /** Log exception here ... */
    }
} 
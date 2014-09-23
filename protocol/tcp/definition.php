<?php
/** Namespace protocol\tcp */
namespace protocol\tcp;

/**
 * Class interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\smtp
 */
class definition extends \library\SMProtocol\abstracts\definition
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 8081;
    }

    public function exception(\Exception $exception)
    {
        /** Log exception here ... */
    }
} 
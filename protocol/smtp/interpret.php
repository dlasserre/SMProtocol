<?php
/** Namespace protocol\smtp */
namespace protocol\smtp;
use engine\server\socket;
use protocol\definition;

/**
 * Class interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\smtp
 */
class interpret extends definition implements \protocol\interfaces\interpret
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 4243;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param socket $socket
     */
    public function transmission(socket $socket)
    {
        $socket->ping('HELO'.PHP_EOL);
        $_response =$socket->pingPong('SAVA'.PHP_EOL);
        echo $_response.PHP_EOL;
        if($_response == "OUI\n\n") {
            $socket->ping('MOI AUSSI !');
        } else {
            $socket->ping('PAS COMPRIS');
        }
    }

    public function exception(\Exception $exception)
    {

    }
} 
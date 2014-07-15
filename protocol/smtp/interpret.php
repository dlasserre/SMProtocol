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
        $this->port = 4242;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param socket $socket
     */
    public function transmission(socket $socket)
    {
        $_data = null;
        echo '---------------------- START DATA ----------------------'.PHP_EOL;
        while($data = $socket->pong(128)) {
            if(strstr($data, "\r\n\r\n")) {
                break;
            } else $_data .= $data;
        }
        echo $_data.PHP_EOL;
        echo '----------------------- END DATA -----------------------'.PHP_EOL;
        $socket->_destruct();
    }

    public function exception(\Exception $exception)
    {

    }
} 
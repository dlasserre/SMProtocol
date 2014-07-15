<?php
/** Namespace protocol\smtp */
namespace protocol\http;
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
        $this->port = 8080;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param socket $socket
     */
    public function transmission(socket $socket)
    {
        $_data = '---------------------- START ['.posix_getpid().'] DATA ----------------------'.PHP_EOL;
        while($data = $socket->pong(128)) {
            if(strstr($data, "\r\n\r\n")) {
                break;
            } else $_data .= $data;
        }
        $_data .= '----------------------- END ['.posix_getpid().'] DATA -----------------------'.PHP_EOL;
        echo $_data.PHP_EOL;
        /** Closing connection */
        $socket->_destruct();
    }

    public function exception(\Exception $exception)
    {

    }
} 
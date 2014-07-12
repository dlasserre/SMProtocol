<?php
/** Namespace engine\server */
namespace engine\server;

/**
 * Class signal
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class signal
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public static function handler($sig)
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1);

        /** module mixing */
        if(is_array(server::$_clients)) {
            foreach(server::$_clients as $client) {
                if(gettype($client) == 'resource') {
                    socket_getpeername($client, $address, $port);
                    echo 'closing connection with '.$address.' on port '.$port.PHP_EOL;
                    socket_close($client);
                }
            }
        }
        /** Close server socket */
        initialize::close();
        exit;
    }
} 
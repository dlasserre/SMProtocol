<?php
/** Namespace engine\server */
namespace engine\server;
use engine\SMProtocol;

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
    public static function handler()
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

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $sig
     */
    public static function handleSMP($sig)
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1);

        switch($sig) {
            case SIGKILL:
                self::_sigKill();
                break;
            case SIGTERM:
                self::_sigTerm();
                break;
            case SIGCHLD:
                self::_sigChld();
                break;
            case SIGHUP:
                self::_sigHup();
                break;
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public static function _sigHup()
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1);
        /** @var array $_copy */
        $_copy = SMProtocol::$_servers;

        foreach($_copy as $pid => $server) {
            echo 'signal received and sending !'.PHP_EOL;
            posix_kill($pid, SIGHUP);
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $sig
     */
    public static function _sigKill($sig = SIGKILL)
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1);
        /** @var array $_copy */
        $_copy = SMProtocol::$_servers;

        foreach($_copy as $pid => $server) {
            posix_kill($pid, $sig);
        }
        exit;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public static  function _sigTerm()
    {
        self::_sigKill();
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public static  function _sigChld()
    {

    }
} 
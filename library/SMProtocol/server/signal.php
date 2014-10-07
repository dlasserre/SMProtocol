<?php
/** Namespace engine\server */
namespace library\SMProtocol\server;
use library\SMProtocol\SMProtocol;

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

        /** Close server socket */
        initialize::close();
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
            case SIGUSR1:
                self::_sigHup();
                break;
            case SIGCHLD:
                self::_sigKill();
                break;
            case SIGINT:
                self::_sigKill();
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
            posix_kill($pid, SIGUSR1);
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

        if(is_array(SMProtocol::$_servers)) {
            /** @var array $_copy */
            $_copy = SMProtocol::$_servers;

            foreach($_copy as $pid => $server) {
                posix_kill($pid, $sig);
            }
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
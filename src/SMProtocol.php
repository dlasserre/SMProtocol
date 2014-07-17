<?php
/** Namespace engine */
namespace engine;
/** Namespace usage */
use engine\exception\client;
use engine\exception\server;
use engine\exception\socket;
use engine\server\signal;
use protocol\definition;

/**
 * Class SMProtocol
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine
 */
class SMProtocol
{
    /** @var  array $_servers */
    public static $_servers;
    /** @var  int $_pid */
    public static $_pid;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        pcntl_signal(SIGINT, array('\engine\server\signal', 'handleSMP'));
        pcntl_signal(SIGTERM, array('\engine\server\signal', 'handleSMP'));
        pcntl_signal(SIGHUP, array($this, 'restart'));
        self::$_pid = posix_getpid();
        $this->launchProtocols();
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $_protocol
     * @return bool
     * @throws exception\SMProtocol
     */
    protected function launchProtocols($_protocol = '*')
    {
        /** @var resource $_dir */
        $_dir = opendir(APPLICATION_PATH.'/protocol/');
        /** @var \src\interfaces\hook $_hooks */
        $_hook = null;
        /** @var array $_exclude_files */
        $_exclude_files = array('interfaces', 'hook.php', 'definition.php', '.', '..');

        include('header.txt');
        /** @var string $directory */
        while($directory = readdir($_dir))
        {
            if($_protocol == '*' or ($_protocol == $directory)) {
                if(file_exists(APPLICATION_PATH.'/protocol/'.$directory)
                    and !in_array($directory, $_exclude_files)) {
                    /** @var string $file */
                    $file = APPLICATION_PATH.'/protocol/'.$directory.'/definition.php';
                    if(file_exists($file)) {
                        SMProtocol::_print(COLOR_RED.'------------ '.strtolower($directory).' ------------'.COLOR_WHITE.PHP_EOL, LOG_LEVEL);
                        /** @noinspection PhpIncludeInspection */
                        require_once($file);
                        /** @var string $_class */
                        $_class = '\protocol\\'.$directory.'\definition';
                        SMProtocol::_print('['.$directory.'] '.COLOR_ORANGE.'Starting...'.COLOR_WHITE.PHP_EOL);
                        /** @var \src\interfaces\hook $_hook */
                        $this->loadHook($directory);
                        try {
                            /** @var int $pid */
                            $pid = pcntl_fork();
                            if($pid < 0) {
                                /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                                throw new \engine\exception\SMProtocol('Impossible to fork protocol server');
                            } else if($pid) {
                                posix_setsid(); // session chief
                                /** increment array of process */
                                self::$_servers[$pid] = array(
                                    'protocol' => $directory,
                                    'start' => mktime()
                                );
                                /** Just for waiting binding and print information in output. */
                                sleep(3);
                            } else { // Child process
                                /** @var \src\abstracts\definition $_instance */
                                $_instance = new $_class();
                                if(!$this->rootPrivilege($_instance->port)) {
                                    SMProtocol::_print(COLOR_RED.$directory.' Stopped...'.COLOR_WHITE.PHP_EOL);
                                    unset($_instance);
                                    continue;
                                }
                                SMProtocol::_print('['.$directory.'] '.COLOR_GREEN.'Success:'.COLOR_WHITE.' detached with pid <'.COLOR_BLUE.posix_getpid().COLOR_WHITE.'>, parent pid <'.COLOR_BLUE.posix_getppid().COLOR_WHITE.'>'.PHP_EOL);
                                SMProtocol::_print(PHP_EOL);
                                /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                                new \engine\server\server($_instance, $directory);
                                exit;
                            }
                        } catch(server $server) {
                            /** Catch server exceptions */
                            if(isset($_instance) and method_exists($_instance, '_exception'))
                                $_instance->_exception($server->getMessage());
                            else SMProtocol::_print($server->getMessage());
                        } catch(client $client) {
                            /** Catch client exceptions */
                            if(isset($_instance) and method_exists($_instance, '_exception'))
                                $_instance->_exception($client->getMessage());
                            else SMProtocol::_print($client->getMessage());
                        }catch(socket $socket ) {
                            /** Catch socket exceptions */
                            if(isset($_instance) and method_exists($_instance, '_exception'))
                                $_instance->_exception($socket->getMessage());
                            else SMProtocol::_print($socket->getMessage());
                        }

                    } else if(!in_array($file, $_exclude_files)) {
                        SMProtocol::_print('['.$directory.'] '.COLOR_RED.'Error: definition file not found...'.COLOR_WHITE.PHP_EOL);
                    }
                }
            }
        }
        /** @var bool $running */
        $running = True;

        while($running) {
            $pid = pcntl_waitpid(-1, $status, WUNTRACED);
            if($pid) {
                $sig = pcntl_wstopsig($status);
                if($sig === SIGHUP) {
                    $this->restartService($pid);
                }
            }
        }
        /** Return */
        return (True);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $_protocol
     * @return null|\protocol\interfaces\hook
     */
    protected function loadHook($_protocol)
    {
        /** @var \protocol\interfaces\hook $_hook */
        $_hook = null;
        /** @var string $hook */
        $_hook_path = APPLICATION_PATH.'/protocol/'.$_protocol.'/hook.php';
        /** @var string $_class_hook */
        $_class_hook = $_protocol.'\hook';

        /** cached file  */
        if(is_file($_hook_path)) {
            /** @noinspection PhpIncludeInspection */
            require_once($_hook_path);
            /** @var \src\interfaces\hook $_hook */
            $_hook = new $_class_hook();
            /** @var array $_interfaces */
            $_interfaces = class_implements($_hook);

            if(!array_key_exists('src\interfaces\hook', $_interfaces)) {
                SMProtocol::_print(COLOR_RED.'['.$_protocol.'] Fail Hook not implement hook interface, hook not loaded'.COLOR_WHITE.PHP_EOL);
                /** @var null $_hook */
                $_hook = null;
            } else
                SMProtocol::_print('['.$_protocol.'] '.COLOR_ORANGE.'Class Hook '.COLOR_GREEN.'loaded'.COLOR_WHITE.PHP_EOL);
        }

        /** Return */
        return ($_hook);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $port
     * @return bool
     */
    protected function rootPrivilege($port)
    {
        /** @var int $uid */
        $uid = posix_getuid();

        if($port <= 1024 and $uid > 0) {
            SMProtocol::_print(COLOR_ORANGE.'Warning: You specified port under 1024, need root privileges to bind socket.'.PHP_EOL);
            /** Return */
            return (False);
        }
        /** Return */
        return (True);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function restart()
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1) {
            /** @var array $_copy */
            $_copy = SMProtocol::$_servers;
            SMProtocol::_print('[SMProtocol] restarting all services...'.PHP_EOL);
            if(is_array($_copy)) {
                foreach($_copy as $pid => $server) {
                    SMProtocol::_print('['.$server['protocol'].'] Shutdown...'.PHP_EOL);
                    posix_kill($pid, SIGKILL);
                }
                /** empty server list */
                SMProtocol::$_servers = null;
            }
            /** Reloading */
            $this->launchProtocols();
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $pid
     */
    public function restartService($pid)
    {
        /** Require since PHP 4.3.0 */
        declare(ticks = 1) {
            if(array_key_exists($pid, self::$_servers)) {
                /** @var array $_copy */
                $_copy = self::$_servers[$pid];

                unset(self::$_servers[$pid]);
                $this->launchProtocols($_copy['protocol']);
            } else /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                throw new \engine\exception\SMProtocol('Impossible to restart service ['.$pid.']');
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $string
     * @param int|bool $_mode
     */
    public static function _print($string, $_mode = LOG_IN_OUTPUT)
    {
        static $mode;

        if(!$mode) $mode = $_mode;

        if($mode == LOG_IN_FILE) {
            file_put_contents(LOG_FILE, $string, FILE_APPEND);
        } else if ($mode == LOG_IN_OUTPUT) {
            echo $string;
        } else if($mode === (LOG_IN_FILE | LOG_IN_OUTPUT)) {
            file_put_contents(LOG_FILE, $string, FILE_APPEND);
            echo $string;
        }
    }
}
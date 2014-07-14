<?php
/** Namespace engine */
namespace engine;
use engine\exception\client;
use engine\exception\server;
use engine\exception\socket;
use engine\server\signal;
use engine\server\SMPCommand\ls;
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
        pcntl_signal(SIGCHLD, array('\engine\server\signal', 'handleSMP'));
        pcntl_signal(SIGTERM, array('\engine\server\signal', 'handleSMP'));

        self::$_pid = posix_getpid();
        $this->launchProtocols();
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return bool
     * @throws exception\SMProtocol
     */
    protected function launchProtocols()
    {
        /** @var resource $_dir */
        $_dir = opendir(APPLICATION_PATH.'/protocol/');

        /** @var string $directory */
        while($directory = readdir($_dir))
        {
            if(is_dir(APPLICATION_PATH.'/protocol/'.$directory)
                and !in_array($directory, array('interfaces', '..', '.'))) {
                /** @var string $file */
                $file = APPLICATION_PATH.'/protocol/'.$directory.'/interpret.php';
                if(file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    require_once($file);
                    /** @var string $_class */
                    $_class = '\protocol\\'.$directory.'\interpret';
                    echo '['.$directory.'] Starting...'.PHP_EOL;
                    try {
                        /** @var int $pid */
                        $pid = pcntl_fork();
                        if($pid < 0) {
                            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                            throw new \engine\exception\SMProtocol('Impossible to fork protocol server');
                        } else if($pid) {
                            pcntl_waitpid(-1, $status, WNOHANG);
                            /** increment array of process */
                            self::$_servers[$pid] = array(
                                'protocol' => $directory,
                                'start' => mktime()
                            );
                            pcntl_signal(SIGHUP, array($this, 'restart'));
                            /** @var bool $running */
                            $running = True;

                            while($running) {
                                pcntl_sigwaitinfo(array(SIGHUP), $info);
                                if(isset($info['signo']) and ((int) $info['signo'] == SIGHUP)) {
                                    $this->restart();
                                    $running = False;
                                }
                            }
                            /** Return */
                            return ($running);
                        } else { // Child process
                            echo '['.$directory.'] server detached with pid <'.posix_getpid().'>, parent pid <'.posix_getppid().'>'.PHP_EOL;
                            /** @var definition $_instance */
                            $_instance = new $_class();
                            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                            new \engine\server\server($_instance, $directory);
                        }
                    } catch(server $server) {
                        /** Catch server exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($server->getMessage());
                        else echo $server->getMessage();
                    } catch(client $client) {
                        /** Catch client exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($client->getMessage());
                        else echo $client->getMessage();
                    }catch(socket $socket ) {
                        /** Catch socket exceptions */
                        if(isset($_instance) and method_exists('_exception', $_instance))
                            $_instance->_exception($socket->getMessage());
                        else echo $socket->getMessage();
                    }
                }
            }
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
        declare(ticks = 1);
        $_copy = SMProtocol::$_servers;

        if(is_array($_copy)) {
            echo '[SMProtocol] restarting...'.PHP_EOL;
            foreach($_copy as $pid => $server) {
                echo '['.$server['protocol'].'] Shutdown...'.PHP_EOL;
                posix_kill($pid, SIGKILL);
            }
            /** empty server list */
            SMProtocol::$_servers = null;
        }
        /** Reloading */
        $this->launchProtocols();
    }

    /**
     * @param $command
     */
    public function mappingCommand($command)
    {
        /** @var array $_parameters */
        $_parameters = explode(' ', $command);
        /** @var string $_command_file */
        $_command_file = APPLICATION_PATH.'/src/server/SMPCommand/'.$_parameters[0].'.php';
        /** @var string $_command */
        $_command = '\engine\server\SMPCommand\\'.$_parameters[0];

        if(is_file($_command_file)) {
            /** @noinspection PhpIncludeInspection */
            require_once($_command_file);
            /** Command server */
            new $_command(array_slice($_parameters, 1));
        }
    }
}
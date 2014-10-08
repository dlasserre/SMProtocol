<?php
/** Namespace engine / server */
namespace library\SMProtocol\server;
/** Usages */
use library\SMProtocol\abstracts\hook;
use library\SMProtocol\engine\server\semaphore;
use library\SMProtocol\engine\server\sender;
use library\SMProtocol\SMProtocol;
use library\SMProtocol\abstracts\definition;
/**
 * Class server
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server
 */
class server extends initialize
{
    /** @var  bool $__server_listening */
    public static $__server_listening;
    /** @var  string $_name */
    protected $_name;
    /** @var  resource[] $_clients */
    public $_clients;
    /** @var  hook[] $_hooks */
    protected $_hooks;

    /** @var  bool $_accept */
    protected $_accept = True;
    /** @var  \download[] $_downloads */
    protected $_downloads;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param definition $definition
     * @param string $_name
     * @throws \library\SMProtocol\exception\server
     */
    public function __construct(definition $definition, $_name)
    {
        /** tick for received signal from children */
        declare(ticks=1);
        /** For defunct process */
        pcntl_signal(SIGCHLD, SIG_IGN);
        if(false !== parent::__construct($definition, $_name)) {
            /** protocol name */
            $this->_name = $_name;
            /** @var \pid $pid */
            $pid = new \pid(posix_getppid(), posix_getpid());
            /** @var mixed $_pid */
            $_pid = null;

            /** While socket is alive */
            for(;;) {
                /** If you want to change max connection by process, please use definition file in your protocol. */
                if((count($this->_clients)+1) == $definition->max_connection
                    and null === $_pid) {
                    SMProtocol::_print('['.$this->_name.'] Children born'.PHP_EOL);
                    /** @var int $_pid */
                    $_pid = pcntl_fork();
                }
                /** Parent process */
                if($_pid and $_pid !== null) {
                    /** Parent close accepted connection */
                    pcntl_waitpid(-1, $status, WNOHANG);
                    SMProtocol::_print('['.$this->_name.'] Parent restart all connections <'.posix_getpid().'>...'.PHP_EOL);
                    $this->_clients = null;
                    /** @var mixed $_pid */
                    $_pid = null;
                } else if(!$_pid or $_pid === null) {
                    if(0 === $_pid) {
                        /** Children process */
                        $this->_accept = False;
                        if (!count($this->_clients))
                            break;
                    }
                    /** @var array $_reads */
                    $_reads = array(parent::$_socket);
                    if(is_array($this->_clients))
                        $_reads = array_merge($_reads, $this->_clients);
                    /** @var array $_write */
                    $_write = $_reads;
                    // Unset parent in socket write, only ready in Input...
                    // all other socket maybe potential write or read.
                    unset($_write[(string)parent::$_socket]);
                    if (@socket_select($_reads, $_write, $_except = null, null) < 1) {
                        SMProtocol::_print('['.$this->_name.'] '.COLOR_ORANGE.'Warning, '.socket_last_error().COLOR_WHITE.PHP_EOL);
                        continue;
                    }
                    /** @var array $_reads */
                    $_reads = array_merge($_reads, $_write);
                    /** @var resource $read */
                    foreach ($_reads as $read) {
                        // SERVER
                        // If socket is parent, accept connection
                        if ($read === parent::$_socket) {
                            /** @var resource $_client */
                            $_client = socket_accept(parent::$_socket);
                            socket_getpeername($_client, $address, $port);
                            if ($_client <= 0) {
                                /** Exception */
                                SMProtocol::_print('[SMProtocol]: ' . COLOR_RED . socket_last_error($_client) . COLOR_WHITE . PHP_EOL);
                            }
                            if ($_client > 0) {
                                $this->_clients[(string)$_client] = $_client;
                                /** @var string $_hook_class */
                                $_hook_class = $this->_name . '\hook';
                                $this->_hooks[(string)$_client] = new $_hook_class(new sender($_client, $definition, $_name));
                                /** Call preDispatching hook method */
                                $this->_hooks[(string)$_client]->preDispatch($address, $port);
                            }
                            // CLIENT
                        } else {
                            // Call dispatch method from hook protocol
                            $this->_hooks[(string)$read]->dispatch();
                            /** If an error occurred, close connection with the client */
                            if(0 !== socket_last_error($read) or $this->_hooks[(string)$read]->isClosed()) {
                                $this->_hooks[(string)$read]->postDispatch(null);
                                SMProtocol::_print('[' . $_name . '] ' . COLOR_RED . 'Connected closed with message "' . $this->_hooks[(string)$read]->getErrorMessage($this->_hooks[(string)$read]->getLastErrorCode()) . '"' . COLOR_WHITE . PHP_EOL);
                                if (gc_enabled()) {
                                    /** @var int $_cycle */
                                    $_cycle = gc_collect_cycles();
                                    $pid->nb_garbage_collector_cycle = $_cycle;
                                    SMProtocol::_print('[' . $_name . ']' . COLOR_BLUE . ' Garbage Collector:' . COLOR_GREEN . ' Number of cycle collected < ' . COLOR_WHITE . $_cycle . COLOR_GREEN . ' >' . COLOR_WHITE . PHP_EOL);
                                }
                                // Process defunct (work in progress for this stat)
                                $pid->defunct = false;
                                if(in_array($this->_hooks[(string)$read]->getDownload()->http_response, array(200, 206))) {
                                    SMProtocol::_print('[' . $this->_name . '] ' . COLOR_ORANGE . 'Save download(s) tracker in database.' . COLOR_WHITE . PHP_EOL);
                                    $this->_hooks[(string)$read]->getDownload()->setPid($pid);
                                    if($_pid !== null) {
                                        $this->_downloads[] = $this->_hooks[(string)$read]->getDownload();
                                        SMProtocol::_print('[' . $this->_name . '] ' . COLOR_ORANGE . 'Stored download(s) completed.' . COLOR_WHITE . PHP_EOL);
                                    } else {
                                        SMProtocol::_print('['.$this->_name.'] '.COLOR_RED.'Connection with all clients was terminated and closed, now save stats in database.'.COLOR_WHITE.PHP_EOL);
                                        $this->storeDownload($this->_hooks[(string)$read]->getDownload());
                                    }
                                }
                                /** close socket, work finish */
                                socket_close($read);
                                unset($this->_clients[(string)$read]);
                                unset($this->_hooks[(string)$read]);
                            }
                        }
                    }
                }
            }
        }
        SMProtocol::_print('['.$this->_name.'] '.COLOR_RED.'Connection with all clients was terminated and closed, now save stats in database.'.COLOR_WHITE.PHP_EOL);
        // Save download after all transactions !
        foreach($this->_downloads as $key => $download) {
            if(in_array($download->http_response, array(200, 206))) {
                $this->storeDownload($download);
            } else {
                SMProtocol::_print('['.$this->_name.'] '.COLOR_RED.' Download not save because HTTP code is '.$download->http_response.COLOR_WHITE.PHP_EOL);
                unset($this->_downloads[$key]);
            }
        }
        /** Killing children, if are here your must children process, the parent process never wipe */
        SMProtocol::_print('['.$this->_name.'] Children <'.posix_getpid().'> wipe'.PHP_EOL);
        /** Sure to send a good signal */
        posix_kill(posix_getpid(), SIGCHLD);
        /** exit */
        exit(SIGCHLD);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param \download $download
     * @return void
     */
    protected function storeDownload(\download $download)
    {
        /** Save all debug during transaction with the server, just for precaution :D */
        foreach(SMProtocol::$_debugs as $debug) {
            $download->addDebug(new \debug($debug));
        }
        $_col = \noSql::getInstance()->selectDB('download')
            ->selectCollection('download');
        if($_col->save($download)) {
            SMProtocol::_print('['.$this->_name.COLOR_BLUE.'@pid:'.$download->_pid->pid.COLOR_WHITE.'] '.COLOR_GREEN.'Save download completed.'.COLOR_WHITE.PHP_EOL);
        } else {
            SMProtocol::_print('['.$this->_name.'] '.COLOR_RED.'Save download fail...'.COLOR_WHITE.PHP_EOL);
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return void
     */
    public function __destruct()
    {
        /** Cleanup all memory usage */
        parent::_cleanup(__CLASS__);
    }
}
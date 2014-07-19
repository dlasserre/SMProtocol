<?php
/** Namespace protocol */
namespace library\SMProtocol\abstracts;
/**
 * Class definition
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol
 */
abstract class definition extends \stdClass implements \library\SMProtocol\interfaces\definition
{
    public $host;
    /** if port < 1024, must run server with root privilege */
    public $port;
    public $max_connection;
    public $block_size = 512;

    public $forward_host;
    public $forward_port;

    public $socket_domain = AF_INET;
    public $socket_type = SOCK_STREAM;
    public $socket_protocol = SOL_TCP;

    public function __destruct()
    {
        exit(SIGCHLD);
    }
} 
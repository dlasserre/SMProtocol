<?php
/** Namespace protocol */
namespace protocol;
/**
 * Class definition
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol
 */
class definition
{
    public $host;
    /** if port < 1024, must run server with root privilege */
    public $port;
    public $max_connection;

    public $forward_host;
    public $forward_port;

    public $socket_domain = AF_INET;
    public $socket_type = SOCK_STREAM;
    public $socket_protocol = SOL_TCP;
} 
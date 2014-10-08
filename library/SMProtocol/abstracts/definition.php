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
    // SERVER CONFIGURATION //
    /** @var  string $host */
    public $host;
    /** if port < 1024, must run server with root privilege */
    public $port;
    /** @var int $max_connection */
    public $max_connection = 5; // Max connection per process, if reached a new children process was created, in the process the multiple socket
    // manage by socket_select system call
    /** @var int $block_size */
    public $block_size = 512; // Only use for read on socket, read per block size.
    /** @var bool $unique_ip */
    public $unique_ip = False; // If multiple download from same IP on same file do not accept connection,
    //but I suggest you not turn at True, because in large company all employed have a same public IP...
    /** @var int $sumaxconn */
    public $sumaxconn = 65535; // This parameter is very important, he define the number of simultaneously sockets manage by the kernel !
    //by default, net.core.somaxconn use, PHP constant SOMAXCONN, i suggest to set at 65535 else the server refused sometime a connection.

    // SERVER FORWARDING CONFIGURATION //
    /** @var  string $forward_host */
    public $forward_host;
    /** @var  int $forward_port */
    public $forward_port;

    // SERVER TRICKY CONFIGURATION //
    /** @var int $socket_domain */
    public $socket_domain = AF_INET;
    /** @var int $socket_type */
    public $socket_type = SOCK_STREAM;
    /** @var int $socket_protocol */
    public $socket_protocol = SOL_TCP;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param array $configuration
     */
    public function __construct(array $configuration) {}

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __destruct()
    {
        /** Exit with sigchld signal */
        exit(SIGCHLD);
    }
} 
<?php
/** Namespace */
namespace engine\server\SMPCommand;
use engine\SMProtocol;

/**
 * Class quit
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server\SMPCommand
 */
class quit
{
    public function __construct(array $parameters)
    {
        posix_kill(SMProtocol::$_pid, SIGKILL);
    }
} 
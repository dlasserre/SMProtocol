<?php
/** Namespace */
namespace engine\server\SMPCommand;
/**
 * Class restart
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package engine\server\SMPCommand
 */
class restart
{
    public function __construct(array $parameters)
    {
        posix_kill($parameters[0], SIGHUP);
    }
} 
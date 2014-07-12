<?php
/** Namespace protocol\interfaces */
namespace protocol\interfaces;
use engine\server\socket;

/**
 * Interface interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\interfaces
 */
interface interpret
{
    public function transmission(socket $socket);
} 
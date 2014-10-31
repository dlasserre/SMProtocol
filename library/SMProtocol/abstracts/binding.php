<?php
/** Namespace */
namespace library\SMProtocol\abstracts;

/**
 * Class binding
 * @author Damien Lasserre <dlasserre@talend.com>
 * @package library\SMProtocol\abstracts
 */
class binding
{
    /** @var  string $host */
    public $host;
    /** @var  int $port */
    public $port;
    /** @var  binding|bool $secure_channel */
    public $other_channel = false;


} 
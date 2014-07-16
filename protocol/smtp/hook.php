<?php
/** Namespace */
namespace smtp;
/** Please make alias for hook */
use protocol\interfaces\hook as hook_interface;

/**
 * Class smtp
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package hook
 */
class hook implements hook_interface
{
    public function preDispatch($address, $port)
    {

    }

    public function dispatching($input)
    {

    }

    public function postDispatch($input)
    {

    }
} 
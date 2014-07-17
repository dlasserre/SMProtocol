<?php
/** Namespace */
namespace smtp;

/**
 * Class smtp
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package hook
 */
class hook extends \hook
{
    /** @var  string $_from */
    protected $_from;
    /** @var  string $_to */
    protected $_to;

    public function preDispatch($address, $port)
    {
        echo 'Connection received from '.$address.' on port '.$port.PHP_EOL;
    }

    public function dispatch($input)
    {
        $this->send('OK 250'.PHP_EOL);
    }

    public function postDispatch()
    {

    }
} 
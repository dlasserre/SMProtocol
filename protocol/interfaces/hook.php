<?php
/**
 * @author Damien Lasserre <dlasserre@talend.com>
 */
namespace protocol\interfaces;
/**
 * Interface hook
 * @author Damien Lasserre <dlasserre@talend.com>
 * @package protocol\interfaces
 */
interface hook
{
    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param string $address
     * @param int $port
     * @return mixed
     */
    public function preDispatch($address, $port);

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param string $input
     * @return mixed
     */
    public function dispatching($input);

    /**
     * @author Damien Lasserre <dlasserre@talend.com>
     * @param string $input
     * @return mixed
     */
    public function postDispatch($input);
} 
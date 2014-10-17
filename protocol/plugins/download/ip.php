<?php
/**
 * Class ip
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class ip
{
    /** @var  MongoId $_id */
    public $_id;
    /** @var  int $id_request */
    public $id_request;
    /** @var  string $ip */
    public $ip;
    /** @var  int $port */
    public $port;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param MongoId $mongoId
     */
    public function __construct(MongoId $mongoId = null)
    {
        if(null === $mongoId)
            $this->_id = new MongoId();
        else $this->_id = $mongoId;
    }
} 
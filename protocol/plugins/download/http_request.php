<?php
/**
 * Class http_request
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class http_request
{
    /** @var  mongoId $_id */
    public $_id;
    /** @var  string $request */
    public $request;
    /** @var  int $_id_parent */
    public $_id_parent;

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
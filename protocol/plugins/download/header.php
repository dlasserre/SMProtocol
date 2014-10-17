<?php
/**
 * Class header
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class header
{
    /** @var string $_id */
    public $_id;
    /** @var  int $id_request */
    public $id_request;
    /** @var  string $header */
    public $header;
    /** @var  string $value */
    public $value;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $id_request
     * @param MongoId $mongoId
     */
    public function __construct($id_request, MongoId $mongoId = null)
    {
        if($id_request) {
            $this->id_request = $id_request;
            if(null === $mongoId)
                $this->_id = new MongoId();
            else $this->_id = $mongoId;
        }
    }
} 
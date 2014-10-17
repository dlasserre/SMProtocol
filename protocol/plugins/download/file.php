<?php
/**
 * Class file
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class file
{
    /** @var  int $_id */
    public $_id;
    /** @var  string $name */
    public $name;
    /** @var  int $size */
    public $size;
    /** @var  string $version */
    public $version;

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
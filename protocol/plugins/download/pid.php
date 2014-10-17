<?php
/**
 * Class pid
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class pid
{
    /** @var  int $_id_pid */
    public $_id;

    /** @var  int $ppid */
    public $ppid;
    /** @var  int $pid */
    public $pid;
    /** @var  int $memory_max_used */
    public $memory_max_used;
    /** @var  int $nb_garbage_collector_cycle */
    public $nb_garbage_collector_cycle;
    /** @var  bool $defunct */
    public $defunct;

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
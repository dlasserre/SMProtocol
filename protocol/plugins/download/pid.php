<?php
/**
 * Class pid
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class pid extends \library\SMProtocol\cleanup
{
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
     * @param int $ppid
     * @param int $pid
     * @param int $memory_max_used
     * @param int $nb_garbage_collector_cycle
     * @param int $defunct
     */
    public function __construct($ppid = null, $pid = null, $memory_max_used = null, $nb_garbage_collector_cycle = null, $defunct = null)
    {
        $this->ppid = $ppid;
        $this->pid = $pid;
        $this->memory_max_used = $memory_max_used;
        $this->nb_garbage_collector_cycle = $nb_garbage_collector_cycle;
        $this->defunct = $defunct;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $class
     */
    public function _cleanup($class = null)
    {
        if(null !== $class)
            parent::_cleanup($class);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __destruct()
    {
        parent::_cleanup(__CLASS__);
    }
} 
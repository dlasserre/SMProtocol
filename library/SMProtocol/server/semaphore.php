<?php
/** Namespace */
namespace library\SMProtocol\engine\server;
use library\SMProtocol\SMProtocol;

/**
 * Class semaphore
 * @package library\SMProtocol\engine\server
 */
class semaphore
{
    /** @var  string $_key */
    protected $_key = null; // Unique key for shmop_open
    /** @var  mixed $_id */
    protected $_segment_id; // Segment of shared memory identifier
    /** @var  int $_shared_size_memory */
    protected $_shared_size_memory; // Size allowed at segment (bytes)

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        if(!function_exists('shmop_open')) {
            SMProtocol::_print('[module:semaphore] '.COLOR_RED.'Module not found, To use shmop you will need to compile PHP with the --enable-shmop parameter in your configure line.'.COLOR_WHITE.PHP_EOL);
            /** Return */
            exit(0);
        }
        SMProtocol::_print('[module:semaphore] '.COLOR_GREEN.'Module semaphore loaded.'.COLOR_WHITE.PHP_EOL);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $_size
     * @return bool|mixed
     */
    public function open($_size = 100)
    {
        $this->_shared_size_memory = $_size;
        if(null === $this->_key) {
            /** @var string _key */
            $this->_key = ftok(__FILE__, 's');
        }
        /** @var mixed $_id */
        if(($_id = shmop_open($this->_key, 'c', 0755, $_size)) < 0) {
            return ( False );
        }
        $this->_segment_id = $_id;
        /** Return */
        return ($this->_segment_id);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param null $size
     * @return bool|string
     */
    public function read($size = null)
    {
        if(null === $size)
            $size = $this->_shared_size_memory;
        if($this->_segment_id) {
            $data = shmop_read($this->_segment_id, 0, $size);
            if(False !== $data) {
                /** Return */
                return ( $data );
            }
        }
        /** Return */
        return ( False );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $data
     * @param int $offset
     * @return bool
     */
    public function write($data, $offset = 0)
    {
        if($this->_segment_id) {
            if(False !== shmop_write($this->_segment_id, $data, $offset)) {
                /** Return */
                return ( True );
            }
        }
        /** Return */
        return ( False );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return bool
     */
    public function close()
    {
        if($this->_key and $this->_segment_id) {
            shmop_close($this->_segment_id);
            /** Return */
            return ( True );
        }
        /** Return */
        return ( False );
    }
} 
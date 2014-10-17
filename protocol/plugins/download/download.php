<?php
/** Requirements */
require_once(APPLICATION_PATH.'/protocol/plugins/download/debug.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/file.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/header.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/http_request.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/ip.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/pid.php');
require_once(APPLICATION_PATH.'/protocol/plugins/download/location.php');
/**
 * Class download
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class download
{
    /** @var  int $_id */
    public $_id;

    // REFERENCES
    /** @var  int $_id_request */
    public $_id_request;
    /** @var  int $_id_pid */
    public $_id_pid;
    /** @var  int $_id_file */
    public $_id_file;

    // INTERN
    /** @var  int $start_at */
    public $start_at;
    /** @var  int $end_at */
    public $end_at;
    /** @var  int $completed */
    public $completed;
    /** @var  int $percent */
    public $percent;
    /** @var  int $bytes_send */
    public $bytes_send = 0;

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
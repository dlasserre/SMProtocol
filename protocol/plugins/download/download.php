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
class download extends \library\SMProtocol\cleanup
{
    /** @var  download $_instance */
    protected static $_instance;
    /** @var  download[] $_stores */
    protected static $_stores;

    /** @var  int $http_response */
    public $http_response;
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

    /** @var  file $file */
    protected $_file;
    /** @var  header[] $headers */
    protected $_headers;
    /** @var  http_request[] $http_request */
    protected $_http_requests;
    /** @var  ip $ip */
    protected $ip;
    /** @var  location $location */
    protected $location;
    /** @var  pid $pid */
    protected $_pid;
    /** @var  debug[] $debugs */
    protected $_debugs;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct(){}

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return download
     */
    public static function getInstance()
    {
        if(!self::$_instance instanceof download) {
            self::$_instance = new download();
        }
        /** Return */
        return (self::$_instance);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param header $header
     * @return download
     */
    public function addHeader(header $header)
    {
        $this->_headers[] = $header;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param file $file
     * @return download
     */
    public function setFile(file $file)
    {
        $this->_file = $file;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param ip $ip
     * @return download
     */
    public function setIp(ip $ip)
    {
        $this->ip = $ip;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param location $location
     * @return download
     */
    public function setLocation(location $location)
    {
        $this->location = $location;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param pid $pid
     * @return download
     */
    public function setPid(pid $pid)
    {
        $this->_pid = $pid;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param debug $debug
     * @return download
     */
    public function addDebug(debug $debug)
    {
        $this->_debugs[] = $debug;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param http_request $request
     * @return download
     */
    public function addHttpRequest(http_request $request)
    {
        $this->_http_requests[] = $request;
        /** Return */
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return int
     */
    public function getId_ip()
    {
        /** @var int $id_ip */
        $id_ip = 0;
        /** @var PDOStatement $ip */
        $ip = \mysql::getInstance()->prepare('SELECT id FROM download_ip WHERE ip = :ip');
        $ip->execute(array(':ip' => $this->ip->ip));
        /** @var array $result */
        $result = $ip->fetch(PDO::FETCH_ASSOC);
        if(isset($result['id'])) {
            $id_ip = $result['id'];
            \library\SMProtocol\SMProtocol::_print('[survey:'.posix_getpid().'] '.COLOR_ORANGE.'ip <'.$this->ip->ip.'> already exist with id <'.$id_ip.'>'.COLOR_WHITE.PHP_EOL);
        } else {
            $ip = \mysql::getInstance()->prepare('INSERT INTO download_ip VALUES (NULL, :ip)');
            if($ip->execute(array(
                ':ip' => $this->ip->ip
            ))) {
                $id_ip = \mysql::getInstance()->lastInsertId();
                \library\SMProtocol\SMProtocol::_print('[survey:'.posix_getpid().'] '.COLOR_BLUE.'ip <'.$this->ip->ip.'> not exist will be created <'.$id_ip.'>'.COLOR_WHITE.PHP_EOL);
            }
        }
        /** Return */
        return ($id_ip);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return int
     */
    public function getId_file()
    {
        /** @var int $id_file */
        $id_file = 0;
        /** @var PDOStatement $file */
        $file = \mysql::getInstance()->prepare('SELECT id FROM download_file WHERE name = :name');
        if(strstr($this->_file->name, '/')) {
            $_file = explode('/', $this->_file->name);
            $this->_file->name = end($_file);
        }
        $file->execute(array(
                ':name' => $this->_file->name
            )
        );
        /** @var array $result */
        $result = $file->fetch(PDO::FETCH_ASSOC);
        if (isset($result['id'])) {
            $id_file = $result['id'];
            \library\SMProtocol\SMProtocol::_print('[survey:'.posix_getpid().'] '.COLOR_BLUE.'file <'.$this->_file->name.'> exist with id <'.$id_file.'>'.COLOR_WHITE.PHP_EOL);
        } else {
            \library\SMProtocol\SMProtocol::_print('[survey:'.posix_getpid().'] '.COLOR_RED.'file <'.$this->_file->name.'> not exist !'.COLOR_WHITE.PHP_EOL);
        }
        /** Return */
        return ( $id_file );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @todo use memcache for curl calling
     * @return bool
     */
    public function save()
    {
        /** @var int $id_ip */
        $id_ip = $this->getId_ip();
        $this->saveRequests();
        if ($this->_file instanceof file) {
            if ($id_file = $this->getId_file()) {
                if ($id_ip) {
                    if ($id_download = $this->saveDownload($id_ip, $id_file)) {
                        \library\SMProtocol\SMProtocol::_print('[survey:' . COLOR_CYAN . posix_getpid() . COLOR_WHITE . '] ' . COLOR_BLUE . 'download was saved with id <' . $id_download . '>' . COLOR_WHITE . PHP_EOL);
                        // Save location
                        $this->saveLocation($id_ip, $id_download);
                        \library\SMProtocol\SMProtocol::_print('[survey:' . COLOR_CYAN . posix_getpid() . COLOR_WHITE . '] ' . COLOR_BLUE . 'location was saved' . COLOR_WHITE . PHP_EOL);
                        // Save header information
                        $this->saveHeaders($id_download);
                        \library\SMProtocol\SMProtocol::_print('[survey:' . COLOR_CYAN . posix_getpid() . COLOR_WHITE . '] ' . COLOR_BLUE . 'headers was saved' . COLOR_WHITE . PHP_EOL);
                        // Save pid
                        $this->savePid($id_download);
                        \library\SMProtocol\SMProtocol::_print('[survey:' . COLOR_CYAN . posix_getpid() . COLOR_WHITE . '] ' . COLOR_BLUE . 'pid was saved' . COLOR_WHITE . PHP_EOL);
                        // Save debug
                        $this->saveDebug($id_download);
                        \library\SMProtocol\SMProtocol::_print('[survey:' . COLOR_CYAN . posix_getpid() . COLOR_WHITE . '] ' . COLOR_BLUE . 'debug was saved' . COLOR_WHITE . PHP_EOL);
                        /** Return */
                        return (true);
                    }
                }
            }
        }
        /** Return */
        return ( false );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $id_ip
     * @param int $id_file
     * @return int
     */
    public function saveDownload($id_ip, $id_file)
    {
        /** @var int $id_download */
        $id_download = 0;
        /** @var PDOStatement $download */
        $download = \mysql::getInstance()->prepare('INSERT INTO download VALUES (
                      NULL, :id_ip, :id_file, :http_response, :start_at, :end_at, :completed, :percent, :bytes_send)');
        if($download->execute(array(
            ':id_ip' => $id_ip,
            ':id_file' => $id_file,
            ':http_response' => $this->http_response,
            ':start_at' => $this->start_at,
            ':end_at' => $this->end_at,
            ':completed' => $this->completed,
            ':percent' => $this->percent,
            ':bytes_send' => $this->bytes_send
        )))
            /** @var int $id_download */
            $id_download = mysql::getInstance()->lastInsertId();
        return ( $id_download );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param int $id_ip
     * @param int $id_download
     */
    public function saveLocation($id_ip, $id_download)
    {
        /** @var resource $curl */
        $curl = curl_init('http://api.talendforge.org/geolocation/index/get/ip/80.250.29.169');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        /** @var string $result */
        $result = curl_exec($curl);
        if(!curl_errno($curl)) {
            /** @var array $result */
            $result = json_decode($result, true);
            if (isset($result[$this->ip->ip]) and is_array($result[$this->ip->ip])) {
                /** @var array $location */
                $location = $result[$this->ip->ip];
                $this->location = new location();
                $this->location->business_zone = $location['BUSINESS_ZONE'];
                $this->location->city = $location['CITY'];
                $this->location->country_code = $location['COUNTRY_CODE'];
                $this->location->country_name = $location['COUNTRY_NAME'];
                $this->location->region = $location['REGION'];
                $this->location->latitude = $location['LATITUDE'];
                $this->location->longitude = $location['LONGITUDE'];
                /** @var PDOStatement $_location */
                $_location = \mysql::getInstance()->prepare('INSERT INTO download_location VALUES (:id_ip, :id_download, :country_code, :country_name, :business_zone, :region, :city, :longitude, :latitude)');
                $_location->execute(array(
                    ':id_ip' => $id_ip,
                    ':id_download' => $id_download,
                    ':country_code' => $this->location->country_code,
                    ':country_name' => $this->location->country_name,
                    ':business_zone' => $this->location->business_zone,
                    ':region' => $this->location->region,
                    ':city' => $this->location->city,
                    ':longitude' => $this->location->longitude,
                    ':latitude' => $this->location->latitude,
                ));
            }
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $id_download
     */
    public function saveHeaders($id_download)
    {
        foreach($this->_headers as $_header) {
            /** @var PDOStatement $header */
            $header = \mysql::getInstance()->prepare('INSERT INTO download_header VALUES (:id_download, :header, :value)');
            $header->execute(array(
                ':id_download' => $id_download,
                ':header' => $_header->header,
                ':value' => $_header->value
            ));
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function saveRequests()
    {
        if(is_array($this->_http_requests)) {
            foreach ($this->_http_requests as $_http_request) {
                /** @var PDOStatement $http */
                $http = \mysql::getInstance()->prepare('INSERT INTO download_http_request VALUES (NULL, :request)');
                $http->execute(array(
                    ':request' => $_http_request->request
                ));
            }
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $id_download
     */
    public function savePid($id_download)
    {
        if($this->_pid instanceof pid) {
            /** @var PDOStatement $_pid */
            $_pid = mysql::getInstance()->prepare('INSERT INTO download_pid VALUES (:id_download, :ppid, :pid, :memory_max_used, :nb_garbage_collector_cycle, :defunct)');
            $_pid->execute(array(
                ':id_download' => $id_download,
                ':ppid' => $this->_pid->ppid,
                ':pid' => $this->_pid->pid,
                ':memory_max_used' => $this->_pid->memory_max_used,
                ':nb_garbage_collector_cycle' => $this->_pid->nb_garbage_collector_cycle,
                ':defunct' => $this->_pid->defunct
            ));
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $id_download
     */
    public function saveDebug($id_download)
    {
        if(is_array($this->_debugs) and count($this->_debugs)) {
            foreach ($this->_debugs as $_debug) {
                /** @var PDOStatement $debug */
                $debug = mysql::getInstance()->prepare('INSERT INTO download_debug VALUES (NULL, :id_download, :output)');
                $debug->execute(array(
                    ':id_download' => $id_download,
                    ':output' => $_debug->output
                ));
            }
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * Store in stack
     */
    public static function store()
    {
        self::$_stores[] = self::$_instance;
        self::$_instance = null;
        /** Return */
        return ( True );
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
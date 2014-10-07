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
    public $_file;
    /** @var  header[] $headers */
    public $_headers;
    /** @var  http_request[] $http_request */
    public $_http_requests;
    /** @var  ip $ip */
    public $ip;
    /** @var  location $location */
    public $location;
    /** @var  pid $pid */
    public $_pid;
    /** @var  debug[] $debugs */
    public $_debugs;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct(){}

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
     * @param string $ip
     */
    public function setLocation($ip)
    {
        /** @var resource $curl */
        $curl = curl_init('http://api.talendforge.org/geolocation/index/get/ip/'.$ip);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        /** @var string $result */
        $result = curl_exec($curl);
        if(!curl_errno($curl)) {
            /** @var array $result */
            $result = json_decode($result, true);
            if (isset($result[$ip]) and is_array($result[$ip])) {
                /** @var array $location */
                $location = $result[$ip];
                $this->location = new location();
                $this->location->business_zone = $location['BUSINESS_ZONE'];
                $this->location->city = $location['CITY'];
                $this->location->country_code = $location['COUNTRY_CODE'];
                $this->location->country_name = $location['COUNTRY_NAME'];
                $this->location->region = $location['REGION'];
                $this->location->latitude = $location['LATITUDE'];
                $this->location->longitude = $location['LONGITUDE'];
            }
        } else {
            echo 'ERROR: '.curl_error($curl).PHP_EOL;
        }
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
        return ($this);
    }
}
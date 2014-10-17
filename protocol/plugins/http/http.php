<?php
/** Namespace */
namespace plugins\http;
/**
 * Class http
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package plugins\http
 */
class http
{
    /** @var  \download $_download */
    //protected $_download;

    /** @var  string $_request */
    private $_request;
    /** @var  string $_method */
    private $_method;
    /** @var  string $_uri */
    public $_uri;
    /** @var  array $_headers */
    private $_headers;
    /** @var  string $_body */
    private $_body;
    /** @var  int $_code */
    public $_code;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param null $_request
     * @param \download $_download
     */
    public function __construct($_request = null, \download &$_download = null)
    {
        //$this->_download = $_download;
        if($_request) {
            $this->_request = $_request;
            $_headers = explode("\r\n", $_request);
            $_method = $_headers[0];
            $parse = explode(' ', $_method);
            if(isset($parse[1]))
                $this->_uri = $parse[1];
            $this->_method = substr($_method[0], 1);
            unset($_headers[0]);
            foreach($_headers as $header) {
                $header = explode(': ', $header);
                if(isset($header[1]))
                    $this->_headers[$header[0]] = trim($header[1]);
            }
            $body = explode("\r\n\r\n", $_request);
            if(isset($body[1]))
                $this->_body = $body[1];
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return string
     */
    public function getUri()
    {
        /** Return */
        return ( $this->_uri );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $name
     * @param string $value
     *
     * @return http
     */
    public function header($name, $value)
    {
        //$this->_download->addHeader(new \header(ucfirst($name), $value));
        $this->_request.= ucfirst($name).': '.$value;
        $this->Crlf();
        /** Return */
        return ($this);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $name
     * @return bool
     */
    public function getHeader($name = null)
    {
        if(null === $name)
            /** Return */
            return ( $this->_headers );
        if(array_key_exists(ucfirst($name), $this->_headers))
            /** Return */
            return ( $this->_headers[$name] );
        /** Return */
        return ( false );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return string
     */
    public function getMethod()
    {
        /** Return */
        return ( $this->_method );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function Crlf()
    {
        $this->_request .= "\r\n";
        return ( $this );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return null|string
     */
    public function __toString()
    {
        return ( $this->_request );
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $content
     */
    public function body($content)
    {
        $this->_request .= $content;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return http
     */
    public function ok()
    {
        //$this->_download->http_response = 200;
        $this->_code = 200;
        $this->_request = 'HTTP/1.1 200 OK';
        $this->Crlf();
        /** Return */
        return ( $this );
    }

    public function notFound()
    {
        //$this->_download->http_response = 404;
        $this->_code = 404;
        $this->_request = 'HTTP/1.1 404 Not Found';
        $this->Crlf();
        /** Return */
        return ( $this );
    }

    public function partial()
    {
        //$this->_download->http_response = 206;
        $this->_code = 206;
        $this->_request = 'HTTP/1.1 206 Partial Content';
        $this->Crlf();
        /** Return */
        return ( $this );
    }

    public function notSatisfiable()
    {
        //$this->_download->http_response = 416;
        $this->_code = 416;
        $this->_request = 'HTTP/1.1 416 Requested Range Not Satisfiable';
        $this->Crlf();
        /** Return */
        return ( $this );
    }
} 
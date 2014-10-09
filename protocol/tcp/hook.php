<?php
/**
 * Approximate current stats download ( from Cecile ).
 * ---------------------------------------------------
 * TOS : 20 000 ( open source product ), without BPM
 * Eval: 1400 (BigData et SendBox)
 * ------------------- APPROXIMATE STATS USAGE ------------------------
 * TOS : 20.000 / 30 = 666  /day
 *     * 666 / 24    = 27   /hour
 *     * 27 / 60     = 0.46 /minutes -> 1 download for ~2 minutes.
 *
 * ------------------
 * Eval: 1400 / 30 = 46   /day
 *     * 46 / 24   = 1.91 /hour
 *     * 1.91/60   = 0.30 /minutes -> 1 download for ~30 minutes.
 */

/** Namespace */
namespace tcp;
/** Requirements */
require_once(APPLICATION_PATH.'/protocol/plugins/http/http.php');
/** Usages */
use library\SMProtocol\SMProtocol;
use plugins\http\http;

/**
 * Class tcp
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package hook
 */
class hook extends \library\SMProtocol\abstracts\hook
{
    /** @var  string $_from */
    protected $_from;
    /** @var  string $_to */
    protected $_to;

    /** @var  string $_file */
    protected $_file;
    /** @var  int $_size */
    protected $_size;
    /** @var int $_offset */
    protected $_offset = -1;
    /** @var  string $_mime_file */
    protected $_mime_file;

    /** @var int $_range_start */
    protected $_range_start = false;
    /** @var int $_range_end */
    protected $_range_end = false;

    /** @var  int $_percent */
    protected $_percent;
    /** @var int $_speed */
    protected $_speed = 256;

    /** @var  \download $_download */
    protected $_download;
    /** @var  bool $_range */
    protected $_range;


    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $address
     * @param int $port
     * @return mixed|void
     */
    public function preDispatch($address, $port)
    {
        /** @var \download _download */
        $this->_download = new \download();
        $this->_download->setLocation($address);
        SMProtocol::_print('Connection received from '.$address.' on port '.$port.PHP_EOL);
        $this->_download->setIp(new \ip($address, $port));
        $this->_download->start_at = time();
        $this->_speed = $this->_speed * 512;

    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return mixed|void
     */
    public function dispatch()
    {
        /** @var http $response */
        $response = new http(null, $this->_download);
        /** @var string $date */
        $date = substr(date(DATE_RFC2822), -6) . ' GMT';

        if($this->_offset == -1) {
            /** @var string $input */
            $input = $this->received();
            /** @var http $request */
            $request = new http($input);

            $this->_download->addHttpRequest(new \http_request($input));
            if ($request->getUri() == '/favicon.ico') {
                SMProtocol::_print('[tcp]' . COLOR_BLUE . ' HTTP Request ask favicon.ico' . COLOR_WHITE . PHP_EOL);
                $response->notFound()
                    ->header('Content-Type', 'text/html; charset=utf-8')
                    ->header('Date', $date)
                    ->header('Allow', 'GET,OPTIONS,HEAD')
                    ->header('Content-Length', 0)
                    ->Crlf();
                $this->send($response->__toString());
                $this->_download->addHttpRequest(new \http_request($response->__toString()));
                $this->close();
            } else {
                /** @var string $file */
                $file = substr($request->_uri, 1);
                /** @var string $_path */
                $_path = APPLICATION_PATH . '/protocol/tcp/files/' . $file;
                if ($request->_uri) {
                    /** File not found or file condition cached */
                    if (!is_file($_path)) {
                        SMProtocol::_print('[tcp] ' . COLOR_BLUE . 'File asked "' . $file . '" ' . COLOR_WHITE . PHP_EOL);
                        /** @var string $_response_text */
                        $_response_text = 'Oups ! File <b>"' . $file . '"</b> not found';
                        $response->notFound()
                            ->header('Content-Type', 'text/html')
                            ->header('Date', $date)
                            ->header('Allow', 'GET,OPTIONS,HEAD')
                            ->header('Content-Length', strlen($_response_text))
                            ->Crlf()
                            ->body($_response_text);
                        $this->send($response->__toString());
                        $this->_download->addHttpRequest(new \http_request($response->__toString()));
                        $this->close();
                    } else {
                        SMProtocol::_print('[tcp] ' . COLOR_BLUE . 'File downloaded "' . $file . '" ' . COLOR_WHITE . PHP_EOL);
                        /** @var string _file */
                        $this->_file = $_path;
                        /** @var int _size */
                        $this->_size = filesize($this->_file);
                        /** @var int $_range */
                        if($_range = $request->getHeader('Range')) {
                            SMProtocol::_print('[tcp] Range received'.$_range.PHP_EOL); // Range debug
                            /** @var int $size_unit */
                            /** @var string $range_orig */
                            list($size_unit, $range_orig) = explode('=', $_range, 2);
                            if ($size_unit == 'bytes') {
                                /** @var string $range */
                                /** @var string $extra_ranges */
                                $explode = explode(',', $range_orig, 2);
                                if(isset($explode[0])) $range =  $explode[0];
                                /** @var int $seek_start */
                                /** @var int $seek_end */
                                list($seek_start, $seek_end) = explode('-', $range, 2);
                                /** @var int $seek_end */
                                $seek_end   = (empty($seek_end)) ? ($this->_size - 1) : min(abs(intval($seek_end)), ($this->_size - 1));
                                /** @var int $seek_start */
                                $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);
                                /** inform header part request 206 HTTP CODE */
                                if ($seek_start >= 0 || $seek_end < ($this->_size - 1)){
                                    /** @var int _range_start */
                                    $this->_range_start = $seek_start;
                                    /** @var int _range_end */
                                    $this->_range_end = $seek_end;
                                    /** @var bool _range */
                                    $this->_range = True;
                                    /** @var int _offset */
                                    $this->_offset = ($seek_start / $this->_speed);
                                }
                            }
                            // HTTP/1.1 416 Requested Range Not Satisfiable
                            else {
                                /** @var string $_response_text */
                                $response->notSatisfiable()
                                    ->header('Content-Type', 'text/html')
                                    ->header('Date', $date)
                                    ->header('Allow', 'GET,OPTIONS,HEAD')
                                    ->header('Content-Length', 0)
                                    ->Crlf()
                                    ->body(null);
                                $this->send($response->__toString());
                                $this->_download->addHttpRequest(new \http_request($response->__toString()));
                                $this->close();
                            }
                        } else $this->_offset++;
                        /** @var resource $finfo */
                        $finfo = finfo_open(FILEINFO_MIME, null);
                        if($finfo) {
                            $filename = APPLICATION_PATH.'/protocol/tcp/files/'.$file;
                            /** application/zip; charset=binary */
                            $this->_mime_file = finfo_file($finfo, $filename);
                        }
                    }
                } else {
                    $this->close();
                }
            }
        } else if ( $this->_file ) {
            /** @var int $size */
            $size = $this->_size;
            /** @var resource $fd */
            // On systems which differentiate between binary and text files (i.e. Windows) the file must be opened with 'b' included in fopen() mode parameter.
            $fd = fopen($this->_file, 'rb');
            $this->_download->setFile(new \file($this->_file, $size, 0));
            /** @var array $_explode */
            $_explode = explode('/', $this->_file);
            /** @var string $filename */
            $filename = end($_explode);
            /** Just one send... :) */
            if($this->_offset == 0 or $this->_range) {
                // ADD header range to inform browser is part request.
                if(False !== $this->_range_start) {
                    SMProtocol::_print('[tcp] '.COLOR_ORANGE.'Partial 206 Response send'.COLOR_WHITE.PHP_EOL);
                    $response->partial()
                        ->header('Content-Range', 'bytes '.(int)trim($this->_range_start).'-'.(int)trim($this->_range_end).'/'.$this->_size)
                        ->header('Content-Length', ($this->_range_end - $this->_range_start + 1));
                } else {
                    SMProtocol::_print('[tcp] '.COLOR_ORANGE.'OK 200 Response send'.COLOR_WHITE.PHP_EOL);
                    $response->ok()
                        ->header('Content-Length', $size);
                }
                if($this->_mime_file !== 'image/png; charset=binary') {
                    $response->header('Content-Type', 'application/octet-stream')
                        ->header('Content-Transfer-Encoding', 'binary')
                        ->header('Accept-Ranges', 'bytes');
                } else {
                    $memcache = new \Memcache();
                    $memcache->addserver('127.0.0.1', 11211);
                    if(!$memcache->get($filename)) {
                        echo 'not in memcache'.PHP_EOL;
                        $memcache->add($filename, file_get_contents($this->_file));
                    } else {
                        echo 'in memcache'.PHP_EOL;
                    }
                    $response->header('Content-Type', 'image/png')
                        ->header('Date', $date)
                        ->Crlf()
                        ->body($memcache->get($filename));
                    $this->send($response->__toString());
                    /** Stats */
                    /** @var int bytes_send */
                    $this->_download->bytes_send = $size;
                    /** @var int percent */
                    $this->_download->percent = 100;
                    $this->_download->completed = 1;
                    $this->_download->addHttpRequest(new \http_request($response->__toString()));
                    $this->close();
                    return;
                };
                // Construct header for response
                $response->header('Cache-Control', 'no-cache')
                    ->header('Date', $date)
                    ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
                    ->header('Allow', 'GET,OPTIONS,HEAD')
                    ->Crlf()
                    ->body(null);
                if (false === $this->send($response->__toString(), 100)) {
                    $this->close();
                }
                $this->_download->addHttpRequest(new \http_request($response->__toString()));
                if($this->_range)
                    $this->_range = False;
            }
            /** @var int $pos */
            $pos = (($this->_offset++) * $this->_speed);
            if($pos > $size) {
                /** @var int $pos */
                $pos = $size;
            }
            /** @var int $_seek */
            $_seek = fseek($fd, $pos, SEEK_SET);
            /** @var int $_debug */
            $_debug = round(($pos/$size) * 100);
            if (!feof($fd) and $_seek >= 0) {
                /** @var mixed $data */
                $data = fread($fd, $this->_speed);
                if (false === $this->send($data, $_debug . '%')) {
                    /** @var int bytes_send */
                    $this->_download->bytes_send = $pos;
                    /** @var int percent */
                    $this->_download->percent = $_debug;
                    /** @var int completed */
                    $this->_download->completed = 0;
                    $this->close();
                }
                /** Close fd */
                fclose($fd);
                /** Browser received data per seconds */
                sleep(1);
            } else {
                $this->close();
            }
            if (!$this->_download->bytes_send) {
                /** @var int bytes_send */
                $this->_download->bytes_send = $size;
                /** @var int percent */
                $this->_download->percent = 100;
            } else $this->_download->completed = 1;
            /** @var int _percent */
            $this->_percent = round(($pos/$size) * 100);
        }
    }

    /**
     * @author Damien Lasserre <dlasserre@gmail.com>
     * @return \download
     */
    public function getDownload()
    {
        /** Return */
        return ($this->_download);
    }

    /**
     * @author Damien Lasserre <dlasserre@gmail.com>
     * @return mixed|void
     */
    public function postDispatch()
    {
        /** @var int end_at */
        $this->_download->end_at = time();
    }
}
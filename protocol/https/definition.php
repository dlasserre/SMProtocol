<?php
/** Namespace protocol\https */
namespace protocol\https;

/**
 * Class interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\smtp
 */
class definition extends \library\SMProtocol\abstracts\definition
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        /** Switch on env */
        switch(APPLICATION_ENV) {
            case 'local':
                $this->channel->host = '127.0.0.1';
                $this->channel->port = 8443;
                break;
            case 'development':
                $this->channel->host = '10.42.10.87';
                $this->channel->port = 8443;
                break;
            case 'production':
            default:
            $this->channel->host = '10.42.10.87';
            $this->channel->port = 8443;
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return array
     */
    public function localPlugin()
    {
        $_configuration = array(
            /** Plugin list configuration */
            'noSql' => array(
                'host' => '127.0.0.1',
                'port' => '27017',
                'db' => 'download'
            )
        );
        /** Return */
        return ($_configuration);
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @return array
     */
    public function developmentPlugin()
    {
        $_configuration = array(
            /** Plugin list configuration */
            'noSql' => array(
                'host' => '127.0.0.1',
                'port' => '27017',
                'db' => 'download'
            )
        );
        /** Return */
        return ($_configuration);
    }

    public function testPlugin()
    {

    }

    public function productionPlugin()
    {

    }

    public function exception(\Exception $exception)
    {
        /** Log exception here ... */
    }
} 
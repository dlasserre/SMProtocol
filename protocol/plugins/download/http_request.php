<?php
/**
 * Class http_request
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class http_request extends \library\SMProtocol\cleanup
{
    /** @var  string $request */
    public $request;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
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
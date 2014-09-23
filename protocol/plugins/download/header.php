<?php
/**
 * Class header
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class header extends \library\SMProtocol\cleanup
{
    /** @var  string $header */
    public $header;
    /** @var  string $value */
    public $value;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $header
     * @param string $value
     */
    public function __construct($header, $value)
    {
        $this->header = $header;
        $this->value = $value;
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
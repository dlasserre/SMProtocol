<?php
/**
 * Class header
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class header
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
} 
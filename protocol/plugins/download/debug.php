<?php
/**
 * Class debug
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class debug
{

    /** @var  int $id */
    public $_id;
    /** @var  string $reference */
    public $reference;
    /** @var  string $output */
    public $output;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $output
     * @param $id
     * @param $reference
     */
    public function __construct($output, $id = null, $reference = null)
    {
        $this->output = $output;
        if( null !== $reference and null !== $id) {
            $this->_id = $id;
            $this->reference = $reference;
        }

    }
} 
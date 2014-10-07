<?php
/**
 * Class debug
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class debug
{
    /** @var  string $output */
    public $output;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }
} 
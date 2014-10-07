<?php
/**
 * Class file
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class file
{
    /** @var  string $name */
    public $name;
    /** @var  int $size */
    public $size;
    /** @var  string $version */
    public $version;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param string $name
     * @param int $size
     * @param string $version
     */
    public function __construct($name, $size, $version)
    {
        $this->name = $name;
        $this->size = $size;
        $this->version = $version;
    }
} 
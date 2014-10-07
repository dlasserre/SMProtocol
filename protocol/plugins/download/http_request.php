<?php
/**
 * Class http_request
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class http_request
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
} 
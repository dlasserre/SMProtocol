<?php
/**
 * Class location
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class location
{
    /** @var  string $_id */
    public $_id;
    /** @var  int $id_request */
    public $id_request;
    /** @var  string $country_code */
    public $country_code;
    /** @var  string $country_name */
    public $country_name;
    /** @var  string $business_zone */
    public $business_zone;
    /** @var  string $region */
    public $region;
    /** @var  string $city */
    public $city;
    /** @var  int $longitude */
    public $longitude;
    /** @var  int $latitude */
    public $latitude;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param MongoId $mongoId
     */
    public function __construct(MongoId $mongoId = null)
    {
        if(null !== $mongoId)
            $this->_id = new MongoId();
        else $this->_id = $mongoId;
    }
}
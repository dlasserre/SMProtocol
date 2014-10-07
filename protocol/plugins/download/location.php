<?php
/**
 * Class location
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 */
class location
{
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
     * @param string $country_code
     * @param string $country_name
     * @param string $business_zone
     * @param string $region
     * @param string $city
     * @param string $longitude
     * @param string $latitude
     */
    public function __construct($country_code = null, $country_name = null, $business_zone = null, $region = null, $city = null, $longitude = null, $latitude = null)
    {
        $this->country_code = $country_code;
        $this->country_name = $country_name;
        $this->business_zone = $business_zone;
        $this->region = $region;
        $this->city = $city;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }
}
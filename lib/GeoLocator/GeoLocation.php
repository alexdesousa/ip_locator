<?php
/**
 * @package GeoLocator
 * @author  Alexander De Sousa <alexanderjosedesousa@gmail.com>
 */
namespace GeoLocator;

/**
 * Array index for GeoLocation object.
 */
define('GL_GEO_LOCATION', 'geo_location');

/**
 * Array index for start IP address.
 */
define('GL_START_IP', 'start_ip');

/**
 * Array index for end IP address.
 */
define('GL_END_IP', 'end_ip');

/**
 * Array index for country code.
 */
define('GL_COUNTRY_CODE', 'country_code');

/**
 * Array index for country name.
 */
define('GL_COUNTRY_NAME', 'country_name');

class BadIpAddressException extends \Exception {}
class BadCountryCodeException extends \Exception {}
class BadCountryNameException extends \Exception {}

/**
 * @brief GeoLocation entity object.
 *
 * @Entity @Table(name="geolocations")
 */
class GeoLocation {
    /**
     * @var int Start IP address.
     * @Id @Column(name="ip_from", type="integer", options={"unsigned"=true})
     */
    protected $startIp;

    /**
     * @var int End IP address.
     * @Id @Column(name="ip_to", type="integer", options={"unsigned"=true})
     */
    protected $endIp;

    /**
     * @var string Country code.
     * @Column(name="country_code", type="string", length=2)
     */
    protected $countryCode;

    /**
     * @var string Country name.
     * @Column(name="country_name", type="string", length=64)
     */
    protected $countryName;

    /**
     * Checks and converts IP to long format.
     *
     * @param $ip IP address.
     *
     * @return Valid long IP address.
     */
    private function ipToLong($ip) {
        if(is_numeric($ip)) {
            if(filter_var(long2ip($ip), FILTER_VALIDATE_IP))
                return intval($ip);
        } else {
            if(filter_var($ip, FILTER_VALIDATE_IP))
                return ip2long($ip);
        }

        throw new BadIpAddressException();
    }

    /**
     * @param string $startIp     Start IP address.
     * @param string $endIP       End IP address.
     * @param string $countryCode Country code.
     * @param string $countryName Country name.
     */
    public function __construct($startIp,
                                $endIp,
                                $countryCode,
                                $countryName) {
        $this->setStartIp($startIp);
        $this->setEndIp($endIp);
        $this->setCountryCode($countryCode);
        $this->setCountryName($countryName);
    }

    /**
     * @return string Gets start IP address.
     */
    public function getStartIp() {
        return strval($this->startIp);
    }

    /**
     * Sets start IP address.
     *
     * @param string $startIp Start IP address.
     */
    public function setStartIp($startIp) {
        $this->startIp = $this->ipToLong($startIp);
    }

    /**
     * @return string Gets end IP address.
     */
    public function getEndIp() {
        return strval($this->endIp);
    }

    /**
     * Sets end IP address.
     *
     * @param string $endIp End IP address.
     */
    public function setEndIp($endIp) {
        $this->endIp = $this->ipToLong($endIp);
    }

    /**
     * @return string Gets country code.
     */
    public function getCountryCode() {
        return $this->countryCode;
    }

    /**
     * Sets country code.
     *
     * @param string $countryCode Country code.
     */
    public function setCountryCode($countryCode) {
        if(!is_string($countryCode))
            throw new BadCountryCodeException();

        $countryCode = trim($countryCode, "\" ");
        if(strlen($countryCode) > 2)
            throw new BadCountryCodeException();

        $this->countryCode = strtoupper($countryCode);
    }

    /**
     * @return string Gets country name.
     */
    public function getCountryName() {
        return $this->countryName;
    }

    /**
     * Sets country name.
     *
     * @param string $countryName Country name.
     */
    public function setCountryName($countryName) {
        if(!is_string($countryName))
            throw new BadCountryNameException();

        $countryName = trim($countryName, "\" ");

        $this->countryName = strtoupper($countryName);
    }

    /**
     * @return array Array representation of GeoLocation object.
     */
    public function toArray() {
        $ip_location = array(
            GL_START_IP     => $this->getStartIp(),
            GL_END_IP       => $this->getEndIp(),
            GL_COUNTRY_CODE => $this->getCountryCode(),
            GL_COUNTRY_NAME => $this->getCountryName()
        );

        return $ip_location;
    }
}

/**
 * Converts an array to a GeoLocation object.
 *
 * @param array $ip_location GeoLocation array representation.
 *
 * @return GeoLocation object.
 */
function fromArray($ip_location) {
    if(!isset($ip_location[GL_START_IP]))
        return NULL;
    $start_ip = $ip_location[GL_START_IP];

    if(!isset($ip_location[GL_END_IP]))
        return NULL;
    $end_ip = $ip_location[GL_END_IP];

    if(!isset($ip_location[GL_COUNTRY_CODE]))
        return NULL;
    $country_code = $ip_location[GL_COUNTRY_CODE];

    if(!isset($ip_location[GL_COUNTRY_NAME]))
        return NULL;
    $country_name = $ip_location[GL_COUNTRY_NAME];

    return new GeoLocation($start_ip, $end_ip, $country_code, $country_name);
}

/**
 * Converts Array of GeoLocations to universal format array.
 *
 * @param array $geoLocations Array of GeoLocations.
 *
 * @return array Universal format array.
 */
function toUniversalFormat($geoLocations) {
    $arr = array();
    foreach($geoLocations as $item) {
        try {
            $arr[] = $item->toArray();
        } catch(Exception $e) {
            continue;
        }
    }
    return $arr;
}

/**
 * Converts from universal format to array of GeoLocations.
 *
 * @param array $array Universal format array.
 *
 * @return array Array of GeoLocations.
 */
function fromUniversalFormat($array) {
    $arr = array();
    foreach($array as $item) {
        try {
            $arr[] = fromArray($item);
        } catch(Exception $e) {
            continue;
        }
    }
    return $arr;
}
?>

<?php
/**
 * @package GeoLocator
 * @author  Alexander De Sousa <alexanderjosedesousa@gmail.com>
 */
namespace GeoLocator;

include_once "GeoLocation.php";
include_once "Ip2LocationExporter.php";

/**
 * @brief Ip2LocationExporter object class.
 */
class IpLocator {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Class constructor. Dependency injection of the entity manager.
     *
     * @param object $entityManager Doctrine entity manager.
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
        $this->em = $entityManager;
    }
    
    /**
     * Geo-locate the given IPV4 address.
     *
     * @param string $ip The IPV4 address.
     *
     * @return GeoLocation or NULL.
     */
    public function geoLocate($ip) {
        $ip = ipToLong($ip);
        $strQuery = 'SELECT g FROM GeoLocator\GeoLocation g WHERE g.startIp <= :ip AND :ip <= g.endIp';
        $query = $this->em->createQuery($strQuery);
        $query->setParameter('ip', $ip);
        $query->setMaxResults(1);
        $result = $query->getResult();
        if(count($result) == 0)
            return NULL;
        return $result[0];
    }
    
    /**
     * Geo-locate the given IPV4 address.
     *
     * @param string $ip       The IPV4 address.
     * @param string $type     File name format.
     * @param string $filename File name.
     *
     * @return GeoLocation or NULL.
     */
    public function find($ip, $filename, $type) {
        $il = new Ip2LocationExporter($this->em);
        return $il->find($ip, $type, $filename);
    }
}
?>

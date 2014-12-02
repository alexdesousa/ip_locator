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
     * @return GeoLocation.
     */
    public function geoLocate($ip) {
        if(($ip = ip2long($ip)) != FALSE) {
            $strQuery = 'SELECT g FROM GeoLocator\GeoLocation g WHERE g.startIp <= :ip AND :ip <= g.endIp';
            $query = $this->em->createQuery($strQuery);
            $query->setParameter('ip', $ip);
            $query->setMaxResults(1);
            return $query->getResult();
        } else
            throw new BadIpAddressException();
    }
}
?>

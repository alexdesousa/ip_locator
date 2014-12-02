<?php
/**
 * @package GeoLocator
 * @author  Alexander De Sousa <alexanderjosedesousa@gmail.com>
 */
namespace GeoLocator;

include_once "GeoLocation.php";

class NotSupportedFormatException extends \Exception {}
class IOException extends \Exception {}

/**
 * @brief Ip2LocationExporter object class.
 */
class Ip2LocationExporter {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * @var Array of GeoLocations.
     */
    private $geoLocations;

    /**
     * Class constructor. Dependency injection of the entity manager.
     *
     * @param object $entityManager Doctrine entity manager.
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * Converts files from [json | csv | xml] to [json | csv | xml].
     *
     * @param string $inputFile     Path to input file
     * @param string $inputFormat   Format of the input file
     *                              (json, csv, xml)
     * @param string $outputFile    Path to output file
     * @param string $outputFormat  Format of the output file
     *                              (json, csv, xml)
     */
    public function export($inputFile, $inputFormat, $outputFile, $outputFormat) {
        $this->from($inputFormat, $inputFile);
        
        if(strtolower($outputFormat) == "db")
            throw new NotSupportedFormatException();
        $this->to($outputFormat, $outputFile);
    }
    
    /**
     * Saves info from [json | csv | xml] to a Sqlite database.
     *
     * @param string $inputFile     Path to input file
     * @param string $inputFormat   Format of the input file
     *                              (json, csv, xml, etc…)
     * @param string $outputFile    Path to output file
     * @param string $outputFormat  Format of the output file
     *                              (json, csv, xml, etc…)
     */
    public function save($inputFile, $inputFormat) {
        $this->from($inputFormat, $inputFile);
        $this->to("db", "");
    }

    /**
     * Converts the implemented formats to GeoLocations array.
     *
     * @param string $type     Input format type.
     * @param string $filename Input file name.
     */
    private function from($type, $filename) {
        $type = strtolower($type);
        if(!file_exists($filename) || !is_readable($filename))
            throw new IOException();

        if(($contents = file_get_contents($filename)) !== FALSE) {
            switch($type) {
            case "json":
                $this->geoLocations = $this->fromJSON($contents);
                break;
            case "xml":
                $this->geoLocations = $this->fromXML($contents);
                break;
            case "csv":
                $this->geoLocations = $this->fromCSV($contents);
                break;
            default:
                throw new NotSupportedFormatException();
            }
        } else
            throw new IOException();
    }

    /**
     * Converts JSON to GeoLocations array.
     *
     * @param string $contents File contents.
     *
     * @return GeoLocations array.
     */
    private function fromJSON($contents) {
        return fromUniversalFormat(json_decode($contents, true));
    }

    /**
     * Converts XML to GeoLocations array.
     *
     * @param string $contents File contents.
     *
     * @return GeoLocations array.
     */
    private function fromXML($contents) {
        $xml = simplexml_load_string($contents);
        $json_array = json_decode(json_encode($xml), true);
        return fromUniversalFormat(
            json_decode(json_encode($xml), true)["geo_location"]);
    }

    /**
     * Converts CSV to GeoLocations array.
     *
     * @param string $contents File contents.
     *
     * @return GeoLocations array.
     */
    private function fromCSV($contents) {
        $data = str_getcsv($contents, "\n");
        $result = array();
        
        foreach($data as $row) {
            $row = str_getcsv($row);
            if(count($row) == 4){
                $result[] = array(
                    GL_START_IP     => $row[0],
                    GL_END_IP       => $row[1],
                    GL_COUNTRY_CODE => $row[2],
                    GL_COUNTRY_NAME => $row[3]
                );
            }
        }
        
        return fromUniversalFormat($result);
    }

    /**
     * Converts a GeoLocations array to any of the implemented formats.
     *
     * @param string $type     Output format type.
     * @param string $filename Output filename.
     *
     * @return GeoLocations in the selected format.
     */
    private function to($type, $filename) {
        $type = strtolower($type);
        switch($type) {
        case "json":
            return $this->toJSON($filename);
        case "xml":
            return $this->toXML($filename);
        case "csv":
            return $this->toCSV($filename);
        case "db":
            return $this->toDB();
        default:
            throw new NotSupportedFormatException();
        }
    }

    /**
     * @param string $filename Filename where the JSON will be written.
     */
    private function toJSON($filename) {
        $result = json_encode(toUniversalFormat($this->geoLocations));
        file_put_contents($filename, $result);
    }
    
    /**
     * @param string $filename Filename where the XML will be written.
     */
    private function toXML($filename) {
        $universal = toUniversalFormat($this->geoLocations);
        $base = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><geo_locations></geo_locations>";
        $xml = new \SimpleXMLElement($base);
        $this->arrayToXML($universal, $xml);
        $result = $xml->asXML();
        file_put_contents($filename, $result);
    }
    
    /**
     * Converts array to XML.
     *
     * @param array  $data Array with the information.
     * @param object $xml  XML.
     */
    private function arrayToXML($data, &$xml) {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(is_numeric($key)) {
                    $subnode = $xml->addChild("geo_location");
                    $this->arrayToXML($value, $subnode);
                } else {
                   $subnode = $xml->addChild("$key");
                   $this->arrayToXML($value, $subnode);
                }
            } else {
                if(is_numeric($key))
                    continue;
                $xml->addChild("$key","$value");
            }
        }
    }
    
    /**
     * @param string $filename Filename where the XML will be written.
     */
    private function toCSV($filename) {
        $universal = toUniversalFormat($this->geoLocations);
        $result = "";
        foreach($universal as $element) {
            if($result != "")
                $result = $result . "\n\"";
            else
                $result = $result . "\"";

            $result = $result .
                $element[GL_START_IP] . "\",\"" .
                $element[GL_END_IP] . "\",\"" .
                $element[GL_COUNTRY_CODE] . "\",\"" .
                $element[GL_COUNTRY_NAME] . "\"";
        }
        file_put_contents($filename, $result);
    }

    /**
     * Creates SQLite database entries for every object read.
     */
    private function toDB() {
        foreach($this->geoLocations as $object) {
            $keys = array( "startIp" => $object->getStartIp(), "endIp" => $object->getEndIp());
            if(is_object($this->em->find('GeoLocator\GeoLocation', $keys)))
                continue;
            $this->em->persist($object);
        }
        $this->em->flush();
    }
}
?>

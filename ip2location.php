#!/usr/bin/env php
<?php
use GeoLocator as GL;

require_once 'bootstrap.php';

define('INPUT_FILE', 'i');
define('INPUT_FORMAT', 'x');
define('OUTPUT_FILE', 'o');
define('OUTPUT_FORMAT', 'y');
define('IP_ADDRESS', 'a');
define('HELP', 'h');
define('EXPORT', 'export');
define('FIND', 'find');
define('SAVE', 'save');
define('LONG_HELP', 'help');

function export($options, $IpExporter) {
    if(!isset($options[INPUT_FILE]))
        export_usage();
    if(!isset($options[INPUT_FORMAT]))
        export_usage();
    if(!isset($options[OUTPUT_FILE]))
        export_usage();
    if(!isset($options[OUTPUT_FORMAT]))
        export_usage();

    $IpExporter->export($options[INPUT_FILE],
                        $options[INPUT_FORMAT],
                        $options[OUTPUT_FILE],
                        $options[OUTPUT_FORMAT]);
}

function export_usage() {
    print "\nUsage: ./ip2location --export -i <input file> -x <xml | csv | json>" .
          "-o <output file> -y <xml | csv | json>\n\n";
    exit(1);
}

function save($options, $IpExporter) {
    if(!isset($options[INPUT_FILE]))
        save_usage();
    if(!isset($options[INPUT_FORMAT]))
        save_usage();

    $IpExporter->save($options[INPUT_FILE],
                      $options[INPUT_FORMAT]);
}

function save_usage() {
    print "\nUsage: ./ip2location --save -i <input file> -x <xml | csv | json>\n\n";
    exit(1);
}

function find($options, $IpLocator) {
    if(!isset($options[IP_ADDRESS]))
        find_usage();

    $result = NULL;
    if(isset($options[INPUT_FILE]) &&
       isset($options[INPUT_FORMAT])) {
        $result = $IpLocator->find($options[IP_ADDRESS],
                                   $options[INPUT_FILE],
                                   $options[INPUT_FORMAT]);
    } else {
        $result = $IpLocator->geoLocate($options[IP_ADDRESS]);
    }
    
    if($result == NULL)
        print "IP not found in the database.";
    else
        print $result->toString() . "\n";
}

function find_usage() {
    print "\nUsage: ./ip2location --find -a <IP address> -i <input file> -x <xml | csv | json>\n\n";
    exit(1);
}

function usage($output) {
    print "\nFind usage: ./ip2location --find -a <IP address> -i <input file> -x <xml | csv | json>\n";
    print "Save usage: ./ip2location --save -i <input file> -x <xml | csv | json>\n";
    print "Export usage: ./ip2location --export -i <input file> -x <xml | csv | json>" .
          "-o <output file> -y <xml | csv | json>\n\n"; 
    exit($output);
}

#Main
$shortopts  = "";
$shortopts .= INPUT_FILE . ":";    // Input file.
$shortopts .= INPUT_FORMAT . ":";  // Input format.
$shortopts .= OUTPUT_FILE . ":";   // Output file.
$shortopts .= OUTPUT_FORMAT . ":"; // Output format.
$shortopts .= IP_ADDRESS . ":";    // IP address.
$shortopts .= HELP;

$longopts  = array(
    EXPORT,   // Export.
    SAVE,     // Find IP address.
    FIND,     // Save in the database.
    LONG_HELP // Long option for help
);
$options = getopt($shortopts, $longopts);

try {
    $IpExporter = new GL\Ip2LocationExporter($entityManager);
    $IpLocator  = new GL\IpLocator($entityManager);

    if(isset($options[EXPORT])) {
        export($options, $IpExporter);
    } else if(isset($options[FIND])) {
        find($options, $IpLocator);
    } else if(isset($options[SAVE])) {
        save($options, $IpExporter);
    } else if (isset($options[HELP]) || isset($options[LONG_HELP])) {
        usage(0);
    } else {
        usage(1);
    }
    exit(0);
} catch(GL\BadIpAddressException $e) {
    print "Bad IP address.\n";
} catch(GL\NotSupportedFormatException $e) {
    print "Not supported file format.\n";
} catch(GL\IOException $e) {
    print "Error reaading or writing file.\n";
}
exit(1);
?>

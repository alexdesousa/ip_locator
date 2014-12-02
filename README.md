# Ip Locator

This project is just a test for file transformations in PHP. The project uses composer to retrieve all its the dependencies and Doctrine as ORM for the database. The application allows:
- Saves information from a file to the database.
- IP location searches on the database or the supplied files.
- Export supported formats to other supported formats (XML, JSON and CSV).

# Installation

You need composer installed globally. To install just run the following commands:

```sh
$ git clone https://github.com/alexdesousa/ip_locator.git
$ cd ip_locator/
$ ./init
```
#Save
To save any file in the Sqlite database.(only supported formats: JSON, XML, CSV), run the following command:
```sh
#Saves ips.json in the database.
$ ./ip2location.php --save -i ips.json -x json
```
#Export
To export any file to other format (only supported formats: JSON, XML, CSV), run the following command:
```sh
#Converts ips.xml to ips.json.
$ ./ip2location.php --export -i ips.xml -x xml -o ips.json -y json
```
#Search in database
To search an IP address in the Sqlite database:
```sh
#Searches IP 4.43.114.16 in the database.
$ ./ip2location.php --find -a 4.43.114.16
```
#Search in file
To search an IP address in a file:
```sh
#Searches IP 4.43.114.16 in ips.xml
$ ./ip2location.php --find -a 4.43.114.16 -i ips.xml -x xml
```
#Data format
The information format in the files should be the following:
- IP Addresses: The data is represented in IP number format.
    ```
    #Assuming IP address as A.B.C.D
    IP Number = A x (256*256*256) + B x (256*256) + C x 256 + D
    ```
- Country code: Two-character country code based on ISO 3166-2.
- Country name: Country name based on ISO 3166-2.

#XML format
The following is the XML format accepted by the application:
```xml
<?xml version="1.0" encoding="utf-8"?>
<geo_locations>
    <geo_location>
        <start_ip>34881536</start_ip>
        <end_ip>34910975</end_ip>
        <country_code>UK</country_code>
        <country_name>UNITED KINGDOM</country_name>
    </geo_location>
    <geo_location>
        <start_ip>69956112</start_ip>
        <end_ip>71020671</end_ip>
        <country_code>US</country_code>
        <country_name>UNITED STATES</country_name>
    </geo_location>
    (...)
</geo_locations>
```
#JSON format
The following is the JSON format accepted by the application:
```json
[{"start_ip":"34881536",
  "end_ip":"34910975",
  "country_code":"UK",
  "country_name":"UNITED KINGDOM"},
 {"start_ip":"69956112",
  "end_ip":"71020671",
  "country_code":"US",
  "country_name":"UNITED STATES"},
  (...)
]
```
#CSV format
The following is the CSV format accepted by the application:
```
"34881536","34910975","UK","UNITED KINGDOM"
"69956112","71020671","US","UNITED STATES"
"84558584","84558847","NL","NETHERLANDS"
"84559520","84559527","ES","SPAIN"
(...)
```
Each line is a record and the values go in the following order:
1. Start IP.
2. End IP.
3. Contry code.
4. Country name.

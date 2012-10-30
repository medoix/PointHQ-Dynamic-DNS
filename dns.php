#!/usr/bin/php
<?php

$username  = "user@example.com";
$api       = "API_KEY_HERE";

// example1.com
$zone_record = array( );
$zone_record["12345"]["123456"] = "example1.com";
$zone_record["12345"]["123456"] = "www.example1.com";
// example2.com
$zone_record["54321"]["543210"] = "example2.com";
$zone_record["54321"]["543210"] = "www.example2.com";

foreach ($zone_record as $zoneId => $zoneArray) {
        foreach ($zoneArray as $recordId => $hostname) {

                // deal with getting the current IP address here.
                $current   = exec("nslookup $hostname dns1.pointhq.com | grep Address | tail -1 | awk '{print $2}'");
                $currentIp = exec("curl http://icanhazip.com/");

                if($current != $currentIp) {
                        // we dont have a match so lets curl some magic for each record.
                        $xml = "<zone-record><data>$currentIp</data><name>$hostname.</name></zone-record>";

                        $ch = curl_init("http://pointhq.com/zones/$zoneId/records/$recordId");
                        $options = array(
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                                        CURLOPT_PORT => 80,
                                        CURLOPT_CUSTOMREQUEST => "PUT",
                                        CURLOPT_HEADER => 1,
                                        CURLOPT_USERPWD => $username . ":" . $api,
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/xml'),
                                        CURLOPT_POSTFIELDS => $xml
                                        );

                        curl_setopt_array($ch, $options);
                        $result = curl_exec($ch);
                }
        }
}

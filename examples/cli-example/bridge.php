#!/usr/bin/php
<?php

// This PHP CLI script demonstrates RESO API login and request functionality using the RESO-PHP SDK
require_once("../../init.php");
require_once("config.php");

RESO\Reso::setDataset('test'); //set in config.php
RESO\Reso::setAccessToken($server_token);
RESO\Reso::setAPIRequestUrl('https://api.bridgedataoutput.com/api/v2/OData/'.\RESO\Reso::getDataset());

// Retrieve top 10 properties from the RESO API endpoint
$data = RESO\Request::request("Property?\$top=10", "json", true);

// Display records
echo "Records retrieved from RESO API: ".count($data["value"])."\n\nRecords:\n";
print_r($data);

return true;
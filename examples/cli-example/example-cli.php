#!/usr/bin/env php
<?php

// This PHP CLI script demonstrates RESO API login and request functionality using the RESO-PHP SDK
require_once("../../init.php");
require_once("config.php");

// Set logging
RESO\Reso::setLogEnabled(true);
RESO\Reso::setLogConsole(true);
RESO\Reso::setLogFile(true);

// Set the variables
RESO\Reso::setClientId($client_id);
RESO\Reso::setClientSecret($client_secret);
RESO\Reso::setAPIAuthUrl($api_auth_url);
RESO\Reso::setAPITokenUrl($api_token_url);
RESO\Reso::setAPIRequestUrl($api_request_url);

// Authorize user
$auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);

// Get access token
RESO\Reso::setAccessToken(RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope));

// Set the Accept header (if needed)
RESO\Request::setAcceptType("json");

// Retrieve metadata from RESO API
/*
$data = RESO\Request::requestMetadata();
// Print Metadata
echo "\nMetadata:\n\n";
print_r($data);
echo "\n\n";
*/

// Retrieve top 10 properties from the RESO API endpoint
$data = RESO\Request::request("Property?\$top=10", "json", true);

// Display records
echo "Records retrieved from RESO API: ".count($data["value"])."\n\nRecords:\n";
print_r($data);

// Save output to file
RESO\Request::requestToFile("test.json", "Property?\$top=10", "json", true);

#!/usr/bin/env php
<?php

// This PHP CLI script demonstrates RESO API login and request functionality using the RESO-PHP SDK
require_once("../../init.php");
require_once("config.php");

// Set the variables
RESO\RESO::setClientId($client_id);
RESO\RESO::setClientSecret($client_secret);
RESO\RESO::setAPIAuthUrl($api_auth_url);
RESO\RESO::setAPIRequestUrl($api_request_url);

// Authorize user
$auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);

// Get access token
RESO\RESO::setAccessToken(RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope));

// Retrieve metadata from RESO API
//$data = RESO\Request::request("\$metadata");

// Retrieve top 10 properties from the RESO API endpoint
$data = RESO\Request::request("Property?\$top=10", "json", true);

// Display records
echo "Records retrieved from RESO API: ".count($data["value"])."\n\nRecords:\n";
print_r($data);

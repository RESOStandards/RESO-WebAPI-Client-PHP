<?php

// This PHP script demonstrates RESO API login handling on client-side (callback)
require_once("../../init.php");
require_once("config.php");

// Set variables
RESO\RESO::setClientId($client_id);
RESO\RESO::setClientSecret($client_secret);
RESO\RESO::setAPITokenUrl($api_token_url);
RESO\RESO::setAPIRequestUrl($api_request_url);

if($_GET['code']) {
    echo "Logged in to RESO API endpoint!";

    // Set access token
    RESO\RESO::setAccessToken($_GET['code']);

    // Do requests ...
}

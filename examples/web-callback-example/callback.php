<?php

// This PHP script demonstrates RESO API login handling on client-side (callback)
require_once("../../init.php");
require_once("config.php");

// Set variables
RESO\Reso::setClientId($client_id);
RESO\Reso::setClientSecret($client_secret);
RESO\Reso::setAPITokenUrl($api_token_url);
RESO\Reso::setAPIRequestUrl($api_request_url);

if($_GET['code']) {
    echo "Logged in to RESO API endpoint!";

    // Set access token
    RESO\Reso::setAccessToken($_GET['code']);

    // Do requests ...
}

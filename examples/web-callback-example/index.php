<?php

// This PHP script demonstrates RESO API login handling on client-side
require_once("../../init.php");
require_once("config.php");

// Set the variables
RESO\Reso::setClientId($client_id);
RESO\Reso::setClientSecret($client_secret);
RESO\Reso::setAPIAuthUrl($api_auth_url);
RESO\Reso::setAPITokenUrl($api_token_url);

// Redirect user to login page
header("Location: ".RESO\OpenIDConnect::getLoginUrl($redirect_uri, $scope));

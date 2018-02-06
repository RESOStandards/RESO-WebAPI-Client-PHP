<?php

// This PHP script demonstrates RESO API login handling on client-side
require_once("../../init.php");
require_once("config.php");

// Set the variables
RESO\RESO::setClientId($client_id);
RESO\RESO::setClientSecret($client_secret);
RESO\RESO::setAPIAuthUrl($api_auth_url);

// Redirect user to login page
header("Location: ".RESO\OpenIDConnect::getLoginUrl($redirect_uri, $scope));

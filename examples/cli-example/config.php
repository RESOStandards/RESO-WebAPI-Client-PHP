<?php

// API authentication URL
$api_auth_url = "";

// API token URL
$api_token_url = "";

// API data request (web) URL
$api_request_url = "";

// API user name
$auth_username = "";

// API password
$auth_password = "";

// API client ID
$client_id = "";

// API secret key
$client_secret = "";

// Redirect URI
$redirect_uri = "";

// bridge server token
$server_token = "";

// Scope
$scope = "ODataApi";

if(file_exists(dirname(__FILE__)."/_config.php")) {
    require_once(dirname(__FILE__)."/_config.php");
}

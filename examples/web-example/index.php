<?php

// Sample application, which demonstrates RESO API PHP SDK use in web applications

require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/config.php');

// Set variables
RESO\Reso::setClientId($client_id);
RESO\Reso::setClientSecret($client_secret);
RESO\Reso::setAPIAuthUrl($api_auth_url);
RESO\Reso::setAPITokenUrl($api_token_url);
RESO\Reso::setAPIRequestUrl($api_request_url);

// Set the Accept header (if needed)
RESO\Request::setAcceptType("json");

// Print head layout
echo '<html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <title>RESO API PHP SDK Web Example</title>
        <style>
        html {
            font-size: 14px;
            font-family: Helvetica, Arial, sans-serif;
        }
        
        body {
            font-size: 14px;
            padding: 1rem;
            margin: 2px;
        }
       
        span {
            margin: 5px;
            padding: 5px;
            line-height: 25px;
        }
        
        #results {
            font-size: 12px;
        }
        
        hr {
            border: 0;
            height: 1px;
            background: #333;
            background-image: linear-gradient(to right, #ccc, #333, #ccc);
        }
        </style>
        </head>
        <center><h1><img src="https://www.reso.org/wp-content/uploads/2016/10/RESO.png" width="160" height="40"> API PHP SDK Web Example</h1>';

        echo '<h4>RESO API PHP SDK version: '.\RESO\Reso::getApiSdkVersion().'<br/>PHP version: '.phpversion().'</h4></center>';

// We do have a login request - process it
if($_POST && isset($_POST['username']) && isset($_POST['password'])) {
    // Get authorization code
    $auth_code = RESO\OpenIDConnect::authorize($_POST['username'], $_POST['password'], $redirect_uri, $scope);
    if(!$auth_code) die("Could not login. Try again.");

    // Get auth token
    $token = RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope);
    if(!$token) die("Could not obtain token.");
    RESO\Reso::setAccessToken($token);

    // Login successful
    echo '<center><h2>Login successful!</h2>
        <form method="post">
            <span><input type="hidden" name="token" value="'.$token.'"></span>
            <span>API Request: <input type="text" name="request" value="Property?$top=10"></span><br/>
            <span>Output format: <select name="format">
                <option value="json" selected>JSON</option>
                <option value="xml">XML</option>
            </select></span><br/>
            <input type="submit" value="Submit request">
        </form></center>';
}
// We do have a logged in request submission
else if($_POST && isset($_POST['token']) && strlen($_POST['token']) > 0 && isset($_POST['request'])) {
    echo '<center><form method="post">
            <input type="hidden" name="token" value="'.$_POST['token'].'">
            <span>API Request: <input type="text" name="request" value="'.$_POST['request'].'"></span><br/>
            <span>Output format: <select name="format">';
            if($_POST['format'] == "xml") {
                echo '<option value="json">JSON</option>
                <option value="xml" selected>XML</option>';
            } else {
                echo '<option value="json" selected>JSON</option>
                <option value="xml">XML</option>';
            }
            echo '</select></span><br/>
            <span><input type="submit" value="Submit request"></span>
        </form></center><hr>
            <h1>Request output:</h1>';

    // Set the access token
    RESO\Reso::setAccessToken($_POST['token']);

    // Process the request
    $data = RESO\Request::request($_POST['request'], $_POST['format']);

    if(!$data) {
        echo "<b>Request failed!</b>";
    } else {
        // Display records
        if ($_POST['format'] == "json") {
            $count = count(json_decode($data, true)["value"]);
            if($count > 0) {
                echo "<b>Records retrieved from RESO API:</b> " . $count . "<br/><br/>";
            } 
            echo "<b>Records:</b><br/>";
            echo "<div id=\"results\">".htmlentities($data)."</div>";
        } else {
            echo "<b>Records:</b><br/><br/><div id=\"results\">" . htmlentities($data) . "</div>";
        }
    }
} else {
    // Display login form
    echo '<center>
    <form method="post">
        <span>Username: <input type="text" name="username" value="'.$auth_username.'"><br/></span>
        <span>Password: <input type="password" name="password" value="'.$auth_password.'"><br/></span>
        <span><input type="submit" value="Login"></span>
    </form></center>';
}

echo '</body></html>';

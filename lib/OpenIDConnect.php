<?php

namespace RESO;

use RESO\Error;
use RESO\Util;

class OpenIDConnect
{
    public static $validInputNamesUsername = array("username", "j_username", "user", "email");
    public static $validInputNamesPassword = array("password", "j_password", "pass");

    /**
     * Autheticates user to the RESO API endpoint and returns authorization code.
     *
     * @param string $username
     * @param string $password
     * @param string $redirect_uri
     * @param string $scope
     *
     * @return string Athorization code.
     */
    public static function authorize($username, $password, $redirect_uri, $scope = "ODataApi")
    {
        \RESO\RESO::logMessage("Initiating RESO API authorization.");

        // Get variables
        $api_auth_url = \RESO\RESO::getAPIAuthUrl();
        $client_id = \RESO\RESO::getClientId();

        $curl = new \RESO\HttpClient\CurlClient();

        // Authentication request parameters
        $params = array(
            "client_id" => $client_id,
            "scope" => $scope,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code"
        );

        // Request authentication
        $response = $curl->request("get", $api_auth_url, null, $params, false)[0];
        $params = @Util\Util::extractFormParameters($response);

        // Do login form POST
        // Build login URL
        $parsed_url = parse_url($api_auth_url);
        if(stripos($params["url"], "{{model.loginUrl}}") !== FALSE) {
            $modelJson = @Util\Util::extractModelJson($response);
            if(!$modelJson || !is_array($modelJson) || !isset($modelJson))
                throw new Error\Api("Could not authenticate to the RESO API auth.");
            $url = $parsed_url["scheme"]."://" .$parsed_url["host"] . $modelJson["loginUrl"];
            foreach($modelJson as $key => $value) {
                if($key == "loginUrl") {
                    continue;
                } else if($key == "antiForgery") {
                    $params["inputs"][$value["name"]] = $value["value"];
                } else {
                    $params["inputs"][$key] = $value;
                }
            }
        } else {
            if (strpos($params["url"], "://") !== FALSE) {
                $url = $params["url"];
            } else {
                $url = $parsed_url["scheme"] . "://" . $parsed_url["host"] . $params["url"];
            }
        }

        // Check if we have valid login url
        if(!parse_url($url))
            throw new Error\Api("Could not obtain RESO API login URL from the response.");
        $params["url"] = $url;

        // Fill in Login parameters
        foreach($params["inputs"] as $key => $value) {
            if($value) continue;
            if(in_array($key, self::$validInputNamesUsername)) {
                $params["inputs"][$key] = $username;
            } else if(in_array($key, self::$validInputNamesPassword) ) {
                $params["inputs"][$key] = $password;
            }
        }
        $headers = array("Content-Type: application/x-www-form-urlencoded");

        // Request login
        $response_curl_info = $curl->request("post", $url, $headers, $params["inputs"], false)[3];

        // Extract code
        $auth_code = @Util\Util::extractCode($response_curl_info["url"]);
        if(!$auth_code)
            throw new Error\Api("Failed to obtain auth code.");

        // Close cURL instance
        $curl->close();

        return $auth_code;
    }

    /**
     * Retrieves the access token of an authorized user session.
     *
     * @param string $redirect_uri
     * @param string $auth_code
     * @param string $scope
     *
     * @return string Access token.
     */
    public static function requestAccessToken($auth_code, $redirect_uri, $scope = "ODataApi")
    {
        \RESO\RESO::logMessage("Sending authorization request to retrieve access token.");

        // Get variables
        $api_token_url = \RESO\RESO::getAPITokenUrl();
        $client_id = \RESO\RESO::getClientId();
        $client_secret = \RESO\RESO::getClientSecret();

        $curl = new \RESO\HttpClient\CurlClient();

        $headers = array(
            'Authorization: Basic '.base64_encode($client_id.":".$client_secret)
        );
        $params = array(
            "grant_type" => "authorization_code",
            "client_id" => $client_id,
            "redirect_uri" => $redirect_uri,
            "code" => $auth_code
        );

        $response = json_decode($curl->request("post", $api_token_url, $headers, $params, false)[0], true);
        if(!$response || !is_array($response) || !isset($response["access_token"]))
            throw new Error\Api("Failed to obtain access token.");

        return $response["access_token"];
    }

    /**
     * Retrieves new access token (refresh).
     *
     * @return string Refreshed access token.
     */
    public static function requestRefreshToken()
    {
        \RESO\RESO::logMessage("Requesting refresh token.");

        // Get variables
        $access_token = \RESO\RESO::getAccessToken();
        $api_token_url = \RESO\RESO::getAPITokenUrl();
        $client_id = \RESO\RESO::getClientId();
        $client_secret = \RESO\RESO::getClientSecret();

        $curl = new \RESO\HttpClient\CurlClient();

        $headers = array(
            'Authorization: Basic '.base64_encode($client_id.":".$client_secret)
        );
        $params = array(
            "grant_type" => "authorization_code",
            "refresh_token" => $access_token
        );

        $response = json_decode($curl->request("post", $api_token_url, $headers, $params, false)[0], true);
        if(!$response || !is_array($response) || !isset($response["refresh_token"]))
            throw new Error\Api("Failed to refresh token.");

        return $response["refresh_token"];
    }

    /**
     * Retrieves RESO API auth login page URL.
     *
     * @param string $redirect_uri
     * @param string $scope
     *
     * @return string RESO API auth login page URL.
     */
    public static function getLoginUrl($redirect_uri, $scope = "ODataApi")
    {
        // Get variables
        $api_auth_url = \RESO\RESO::getAPIAuthUrl();
        $client_id = \RESO\RESO::getClientId();

        // Authentication request parameters
        $params = array(
            "client_id" => $client_id,
            "scope" => $scope,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code"
        );

        return $api_auth_url . '?' . http_build_query($params);
    }
}

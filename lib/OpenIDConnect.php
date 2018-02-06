<?php

namespace RESO;

use RESO\Error;
use RESO\Util;

class OpenIDConnect
{
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
        // Get variables
        $api_auth_url = \RESO\RESO::getAPIAuthUrl();
        $client_id = \RESO\RESO::getClientId();

        $curl = new \RESO\HttpClient\CurlClient();

        // Build auth request URL
        $url = $api_auth_url . "authorize";

        // Authentication request parameters
        $params = array(
            "client_id" => $client_id,
            "scope" => $scope,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code"
        );

        // Request authentication
        $response = $curl->request("get", $url, null, $params, false)[0];

        // Convert the modelJson response to array
        $response_array = @Util\Util::extractModelJson($response);
        if(!$response_array || !is_array($response_array) || !isset($response_array["antiForgery"]) || !isset($response_array["loginUrl"]))
            throw new Error\Api("Could not authenticate to the RESO API auth.");

        // Do login form POST
        // Build login URL
        $parsed_url = parse_url($api_auth_url);
        $url = $parsed_url["scheme"]."://" .$parsed_url["host"] . $response_array["loginUrl"];

        // Login parameters
        $params = array(
            $response_array["antiForgery"]["name"] => $response_array["antiForgery"]["value"],
            "username" => $username,
            "password" => $password
        );
        $headers = array("Content-Type: application/x-www-form-urlencoded");

        // Request login
        $response_curl_info = $curl->request("post", $url, $headers, $params, false)[3];

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
        // Get variables
        $api_auth_url = \RESO\RESO::getAPIAuthUrl();
        $client_id = \RESO\RESO::getClientId();
        $client_secret = \RESO\RESO::getClientSecret();

        $curl = new \RESO\HttpClient\CurlClient();

        // Build token request URL
        $url = $api_auth_url . "token";
        $headers = array(
            'Authorization: Basic '.base64_encode($client_id.":".$client_secret)
        );
        $params = array(
            "grant_type" => "authorization_code",
            "client_id" => $client_id,
            "redirect_uri" => $redirect_uri,
            "code" => $auth_code
        );

        $response = json_decode($curl->request("post", $url, $headers, $params, false)[0], true);
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
        // Get variables
        $access_token = \RESO\RESO::getAccessToken();
        $api_auth_url = \RESO\RESO::getAPIAuthUrl();
        $client_id = \RESO\RESO::getClientId();
        $client_secret = \RESO\RESO::getClientSecret();

        $curl = new \RESO\HttpClient\CurlClient();

        // Build token request URL
        $url = $api_auth_url . "token";
        $headers = array(
            'Authorization: Basic '.base64_encode($client_id.":".$client_secret)
        );
        $params = array(
            "grant_type" => "authorization_code",
            "refresh_token" => $access_token
        );

        $response = json_decode($curl->request("post", $url, $headers, $params, false)[0], true);
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

        // Build auth request URL
        $url = $api_auth_url . "authorize";

        // Authentication request parameters
        $params = array(
            "client_id" => $client_id,
            "scope" => $scope,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code"
        );

        return $url . '?' . http_build_query($params);
    }
}

<?php

namespace RESO;

use RESO\Error;

/**
 * Class Reso
 *
 * @package RESO
 */
class RESO
{
    // @var string The RESO API client_id to be used for auth and query requests.
    public static $clientId;

    // @var string The RESO API client_secret to be used for auth and query requests.
    public static $clientSecret;

    // @var string The RESO API access token.
    public static $accessToken;

    // @var string The base URL for RESO API Auth service.
    public static $apiAuthUrl = '';

    // @var string The base URL for RESO API Request service.
    public static $apiRequestUrl = '';

    // @var boolean Defaults to false.
    public static $verifySslCerts = false;

    // @var string RESO API PHP SDK version.
    public static $apiSdkVersion = '1.0.0';

    /**
     * @return string The RESO API client_id used for auth and query requests.
     */
    public static function getClientId()
    {
        if(!self::$clientId) throw new Error\Reso("API client_id is not set.");
        return self::$clientId;
    }

    /**
     * Sets the RESO API client_id to be used for auth and query requests.
     *
     * @param string $clientId
     */
    public static function setClientId($clientId)
    {
        self::$clientId = $clientId;
    }

    /**
     * @return string The RESO API client_secret used for auth and query requests.
     */
    public static function getClientSecret()
    {
        if(!self::$clientSecret) throw new Error\Reso("API client_secret is not set.");
        return self::$clientSecret;
    }

    /**
     * Sets the RESO API client_secret to be used for requests.
     *
     * @param string $clientSecret
     */
    public static function setClientSecret($clientSecret)
    {
        self::$clientSecret = $clientSecret;
    }

    /**
     * @return string The RESO API access token.
     */
    public static function getAccessToken()
    {
        return self::$accessToken;
    }

    /**
     * Sets the RESO API access token.
     *
     * @param string $accessToken
     */
    public static function setAccessToken($accessToken)
    {
        self::$accessToken = $accessToken;
    }

    /**
     * @return string The RESO API auth endpoint URL.
     */
    public static function getAPIAuthUrl()
    {
        if(!self::$apiAuthUrl) throw new Error\Reso("API auth endpoint URL is not set.");
        return self::$apiAuthUrl;
    }

    /**
     * Sets the RESO API auth endpoint URL.
     *
     * @param string $apiAuthUrl
     */
    public static function setAPIAuthUrl($apiAuthUrl)
    {
        self::$apiAuthUrl = $apiAuthUrl;
    }

    /**
     * @return string The RESO API request endpoint URL.
     */
    public static function getAPIRequestUrl()
    {
        if(!self::$apiRequestUrl) throw new Error\Reso("API request endpoint URL is not set.");
        return self::$apiRequestUrl;
    }

    /**
     * Sets the RESO API request endpoint URL.
     *
     * @param string $apiRequestUrl
     */
    public static function setAPIRequestUrl($apiRequestUrl)
    {
        self::$apiRequestUrl = $apiRequestUrl;
    }


    /**
     * @return string The RESO API SDK version.
     */
    public static function getApiSdkVersion()
    {
        return self::$apiSdkVersion;
    }

    /**
     * @return boolean True / false to verify SSL certs in cURL requests.
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * Sets true / false to verify SSL certs in cURL requests.
     *
     * @param boolean $verify
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }
}

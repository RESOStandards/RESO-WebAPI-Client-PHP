<?php

namespace RESO;

use RESO\Log;
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

    // @var string The authentication / authorization URL for RESO API Auth service.
    public static $apiAuthUrl = '';

    // @var string The token request URL for RESO API Auth service.
    public static $apiTokenUrl = '';

    // @var string The base URL for RESO API Request service.
    public static $apiRequestUrl = '';

    // @var boolean Defaults to false.
    public static $verifySslCerts = false;

    // @var string RESO API PHP SDK version.
    public static $apiSdkVersion = '1.0.0';

    // @var bool Logging (overall) enabled / disabled.
    public static $logEnabled = false;

    // @var bool Logging to console enabled / disabled.
    public static $logToConsole = true;

    // @var bool Logging to file enabled / disabled.
    public static $logToFile = false;

    // @var string Log file name enabled / disabled.
    public static $logFileName = 'out.log';

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
        self::logMessage("Setting RESO API client id to '".$clientId."'.");
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
        self::logMessage("Setting RESO API client secret.");
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
        self::logMessage("Setting RESO API access token.");
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
        self::logMessage("Setting RESO API auth URL to '".$apiAuthUrl."'.");
        self::$apiAuthUrl = $apiAuthUrl;
    }

    /**
     * @return string The RESO API token endpoint URL.
     */
    public static function getAPITokenUrl()
    {
        if(!self::$apiTokenUrl) throw new Error\Reso("API token endpoint URL is not set.");
        return self::$apiTokenUrl;
    }

    /**
     * Sets the RESO API token endpoint URL.
     *
     * @param string $apiTokenUrl
     */
    public static function setAPITokenUrl($apiTokenUrl)
    {
        self::logMessage("Setting RESO API token URL to '".$apiTokenUrl."'.");
        self::$apiTokenUrl = $apiTokenUrl;
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
        self::logMessage("Setting RESO API request URL to '".$apiRequestUrl."'.");
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
     * @param boolean $bool
     */
    public static function setVerifySslCerts($bool)
    {
        self::logMessage("Setting SSL certificate verification to '".(string)$bool."'.");
        self::$verifySslCerts = $bool;
    }

    /**
     * @return boolean True / false if logging (overall) is enabled.
     */
    public static function getLogEnabled() {
        return self::$logEnabled;
    }

    /**
     * Sets true / false to enable logging (overall).
     *
     * @param boolean $bool
     */
    public static function setLogEnabled($bool) {
        self::$logEnabled = $bool;
    }

    /**
     * @return boolean True / false if output logging to console.
     */
    public static function getLogConsole() {
        return self::$logToConsole;
    }

    /**
     * Sets true / false to enable logging to console.
     *
     * @param boolean $bool
     */
    public static function setLogConsole($bool) {
        self::$logToConsole = $bool;
    }

    /**
     * @return boolean True / false if output logging to file.
     */
    public static function getLogFile() {
        return self::$logToFile;
    }

    /**
     * Sets true / false to enable logging to file.
     *
     * @param boolean $bool
     */
    public static function setLogFile($bool) {
        self::$logToFile = $bool;
    }

    /**
     * @return string File path of the log file (if logging to file).
     */
    public static function getLogFileName() {
        return self::$logFileName;
    }

    /**
     * Sets log file name (if logging to file).
     *
     * @param string $file_name
     */
    public static function setLogFileName($file_name) {
        self::$logFileName = $file_name;
    }

    /**
     * Logs message.
     *
     * @param string $message
     */
    public static function logMessage($message) {
        Log\Log::logMessage($message);
    }
}

<?php

namespace RESO\HttpClient;

use RESO\Reso;
use RESO\Error;
use RESO\Util;

class CurlClient implements ClientInterface
{
    private static $instance;

    private $curl = null;
    private static $isCurlAvailable = null;
    protected $defaultOptions;
    protected $userAgentInfo;
    private $cookieFile = ".resocookie";
    const DEFAULT_TIMEOUT = 80;
    const DEFAULT_CONNECT_TIMEOUT = 30;
    const SDK_VERSION = '1.0.0';
    private $timeout = self::DEFAULT_TIMEOUT;
    private $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * CurlClient constructor.
     *
     * Pass in a callable to $defaultOptions that returns an array of CURLOPT_* values to start
     * off a request with, or an flat array with the same format used by curl_setopt_array() to
     * provide a static set of options. Note that many options are overridden later in the request
     * call, including timeouts, which can be set via setTimeout() and setConnectTimeout().
     *
     * Note that request() will silently ignore a non-callable, non-array $defaultOptions, and will
     * throw an exception if $defaultOptions returns a non-array value.
     *
     * @param array|callable|null $defaultOptions
     */
    public function __construct($defaultOptions = null)
    {
        $this->defaultOptions = $defaultOptions;
        $this->initUserAgentInfo();

        self::$isCurlAvailable = extension_loaded("curl");

        if (!self::$isCurlAvailable) {
            throw new Error\Reso("It looks like the cURL extension is not enabled. " .
                "cURL extension is required to use the RESO API PHP SDK.");
        }

        if(file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }

    public function __destruct() {
        if($this->curl) {
            $this->close();
        }

        if(file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }

    public function initUserAgentInfo()
    {
        $curlVersion = curl_version();
        $this->userAgentInfo = array(
            'httplib' =>  'curl ' . $curlVersion['version'],
            'ssllib'  => $curlVersion['ssl_version'],
            'sdkInfo' => "RESO-RETS-SDK/" . self::SDK_VERSION
        );
    }

    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    public function getUserAgentInfo()
    {
        return $this->userAgentInfo;
    }

    public function setTimeout($seconds)
    {
        $this->timeout = (int) max($seconds, 0);
        return $this;
    }

    public function setConnectTimeout($seconds)
    {
        $this->connectTimeout = (int) max($seconds, 0);
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        if($headers == null || !is_array($headers)) {
            $headers = array();
        }

        if(!$this->curl) {
            $this->curl = curl_init();
        }
        $method = strtolower($method);

        $opts = array();
        if (is_callable($this->defaultOptions)) { // call defaultOptions callback, set options to return value
            $opts = call_user_func_array($this->defaultOptions, func_get_args());
            if (!is_array($opts)) {
                throw new Error\Api("Non-array value returned by defaultOptions CurlClient callback");
            }
        } elseif (is_array($this->defaultOptions)) { // set default curlopts from array
            $opts = $this->defaultOptions;
        }

        if ($method == 'get') {
            if ($hasFile) {
                throw new Error\Api(
                    "Issuing a GET request with a file parameter"
                );
            }
            $opts[CURLOPT_HTTPGET] = 1;
            if (is_array($params) && count($params) > 0) {
                $encoded = Util\Util::urlEncode($params);
                $absUrl = "$absUrl?$encoded";
            }
        } elseif ($method == 'post') {
            $opts[CURLOPT_POST] = count($params);
            $opts[CURLOPT_POSTFIELDS] = $hasFile ? $params : Util\Util::urlEncode($params);
        } else {
            throw new Error\Api("Unrecognized method $method");
        }

        // Create a callback to capture HTTP headers for the response
        $rheaders = array();
        $headerCallback = function ($curl, $header_line) use (&$rheaders) {
            // Ignore the HTTP request line (HTTP/1.1 200 OK)
            if (strpos($header_line, ":") === false) {
                return strlen($header_line);
            }
            list($key, $value) = explode(":", trim($header_line), 2);
            $rheaders[trim($key)] = trim($value);
            return strlen($header_line);
        };

        $absUrl = Util\Util::utf8($absUrl);
        $opts[CURLOPT_URL] = $absUrl;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_FOLLOWLOCATION] = true;
        $opts[CURLOPT_AUTOREFERER] = true;
        $opts[CURLOPT_COOKIESESSION] = true;
        $opts[CURLOPT_COOKIEJAR] = $this->cookieFile;
        $opts[CURLOPT_COOKIEFILE] = $this->cookieFile;
        $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[CURLOPT_TIMEOUT] = $this->timeout;
        $opts[CURLOPT_HEADERFUNCTION] = $headerCallback;
        if($headers) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }
        if (!RESO::$verifySslCerts) {
            $opts[CURLOPT_SSL_VERIFYHOST] = false;
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
        }

        curl_setopt_array($this->curl, $opts);
        $rbody = curl_exec($this->curl);

        if ($rbody === false) {
            $errno = curl_errno($this->curl);
            $message = curl_error($this->curl);
            $this->handleCurlError($absUrl, $errno, $message);
        }

        $curlInfo = curl_getinfo($this->curl);
        return array($rbody, $curlInfo["http_code"], $rheaders, $curlInfo);
    }

    public function close() {
        if($this->curl) {
            curl_close($this->curl);
            $this->curl = null;
        }
    }

    /**
     * @param number $errno
     * @param string $message
     * @throws Error\ApiConnection
     */
    private function handleCurlError($url, $errno, $message)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to RESO API ($url).";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify RESO API endpoint's SSL certificate.  Please make sure "
                    . "that your network is not intercepting certificates.  "
                    . "(Try going to $url in your browser.)  "
                    . "If this problem persists,";
                break;
            default:
                $msg = "Unexpected error communicating with RESO. "
                    . "If this problem persists,";
        }
        $msg .= " let us know at info@reso.org.";

        $msg .= "\n\n(Network error [errno $errno]: $message)";
        throw new Error\ApiConnection($msg);
    }
}

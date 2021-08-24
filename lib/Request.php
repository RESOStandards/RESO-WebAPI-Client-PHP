<?php

namespace RESO;

use RESO\Error;
use RESO\Util;

abstract class Request
{
    private static $validOutputFormats = array("json", "xml");
    private static $requestAcceptType = "";
	private static $requestAcceptEncoding = "";
    /**
     * Sends GET request and returns output in specified format.
     *
     * @param string $request
     * @param string $output_format
     * @param string $decode_json
     * @param string $accept_format
     *
     * @return mixed API Request response in requested data format.
     */
    public static function request($request, $output_format = "xml", $decode_json = false)
    {
        \RESO\RESO::logMessage("Sending request '".$request."' to RESO API.");

        // Get variables
        $api_request_url = \RESO\RESO::getAPIRequestUrl();
        $token = \RESO\RESO::getAccessToken();

        if(!in_array($output_format, self::$validOutputFormats)) {
            $output_format = "json";
        }

        $curl = new \RESO\HttpClient\CurlClient();

        // Parse and validate request parameters
        $request = self::formatRequestParameters($request);

        // Build request URL
        $url = rtrim($api_request_url, "/") . "/" . $request;

        // Set the accept type
        if(self::$requestAcceptType) {
            $accept = "application/".self::$requestAcceptType;
        } else {
            $accept = "*/*";
        }

        // Set headers
        $headers = array(
            "Accept: ".$accept,
            "Authorization: Bearer ".$token
        );
		/// enable GZIP compression for post MLSGRID Api
		if(self::$requestAcceptEncoding) {
		 	$headers[] = "Accept-Encoding:".self::$requestAcceptEncoding;
		}
        // Send request
        $response = $curl->request("get", $url, $headers, null, false);
        if(!$response || !is_array($response) || $response[1] != 200) {
            switch($response[1]) {
                case "406":
                    throw new Error\Api("API returned HTTP code 406 - Not Acceptable. Please, setup a valid Accept type using Request::setAcceptType(). Request URL: " . $api_request_url . "; Request string: " . $request . "; Response: " . $response[0]);
                default:
                    throw new Error\Api("Could not retrieve API response. Request URL: " . $api_request_url . "; Request string: " . $request . "; Response: " . $response[0]);
            }
        }

        // Decode the JSON response to PHP array, if $decode_json == true
        $is_json = Util\Util::isJson($response[0]);
        if($is_json && $output_format == "json" && $decode_json) {
            $return = json_decode($response[0], true);
            if(!is_array($response))
                throw new Error\Api("Could not decode API response. Request URL: ".$api_request_url."; Request string: ".$request."; Response: ".$response[0]);
        } elseif($is_json && $output_format == "xml") {
            $return = Util\Util::arrayToXml(json_decode($response[0], true));
        } else {
            $return = $response[0];
        }

        return $return;
    }

    /**
     * Sends POST request with specified parameters.
     *
     * @param string $request
     * @param array $params
     * @param string $accept_format
     *
     * @return mixed API Request response.
     */
    public static function requestPost($request, $params = array())
    {
        \RESO\RESO::logMessage("Sending POST request '".$request."' to RESO API.");

        // Get variables
        $api_request_url = \RESO\RESO::getAPIRequestUrl();
        $token = \RESO\RESO::getAccessToken();

        $curl = new \RESO\HttpClient\CurlClient();

        // Build request URL
        $url = rtrim($api_request_url, "/") . "/" . $request;

        // Set the accept type
        if(self::$requestAcceptType) {
            $accept = "application/".self::$requestAcceptType;
        } else {
            $accept = "*/*";
        }

        $headers = array(
            "Accept: ".$accept,
            "Authorization: Bearer ".$token
        );
		if(self::$requestAcceptEncoding) {
		 	$headers[] = "Accept-Encoding:".$requestAcceptEncoding;
		}

        // Send request
        $response = $curl->request("post", $url, $headers, $params, false);
        if(!$response || !is_array($response) || $response[1] != 200) {
            switch($response[1]) {
                case "406":
                    throw new Error\Api("API returned HTTP code 406 - Not Acceptable. Please, setup a valid Accept type using Request::setAcceptType(). Request URL: " . $api_request_url . "; Request string: " . $request . "; Response: " . $response[0]);
                default:
                    throw new Error\Api("Could not retrieve API response. Request URL: " . $api_request_url . "; Request string: " . $request . "; Response: " . $response[0]);
            }
        }

        // Decode the JSON response
        $is_json = Util\Util::isJson($response[0]);
        if($is_json) {
            $return = json_decode($response[0], true);
        } else {
            $return = $response[0];
        }

        return $return;
    }

    /**
     * Requests RESO API output and saves the output to file.
     *
     * @param string $file_name
     * @param string $request
     * @param string $output_format
     * @param bool $overwrite
     *
     * @return True / false output saved to file.
     */
    public static function requestToFile($file_name, $request, $output_format = "xml", $overwrite = false, $accept_format = "json") {
        \RESO\RESO::logMessage("Sending request '".$request."' to RESO API and storing output to file '".$file_name."'.");

        if(!$overwrite && is_file($file_name)) {
            throw new Error\Reso("File '".$file_name."' already exists. Use variable 'overwrite' to overwrite the output file.");
        }

        if(!is_dir(dirname($file_name))) {
            throw new Error\Reso("Directory '".dir($file_name)."' does not exist.");
        }

        $output_data = self::request($request, $output_format, false, $accept_format);
        if(!$output_data) {
            \RESO\RESO::logMessage("Request output save to file failed - empty or erroneous data.");
            return false;
        }

        file_put_contents($file_name, $output_data);
        if(!is_file($file_name)) {
            \RESO\RESO::logMessage("Request output save to file failed - could not create output file.");
            return false;
        }

        \RESO\RESO::logMessage("Request output save to file succeeded.");
        return true;
    }

    /**
     * Requests RESO API metadata output.
     *
     * @return Metadata request output.
     */
    public static function requestMetadata() {
        \RESO\RESO::logMessage("Requesting resource metadata.");
        return self::request("\$metadata");
    }

    /**
     * Sets accept Accept content type in all requests.
     *
     * @param string
     */
    public static function setAcceptType($type = "") {
        if(in_array($type, self::$validOutputFormats)) {
            self::$requestAcceptType = $type;
        }
    }
	
    /**
     * Formats request parameters to compatible string
     *
     * @param string
     */
    public static function formatRequestParameters($parameters_string) {
        parse_str($parameters_string, $parsed);
        if(!is_array($parsed) || empty($parsed)) {
            throw new Error\Reso("Could not parse the request parameters.");
        }

        $params = array();
        foreach($parsed as $key => $param) {
            if($param) {
                $params[] = $key . "=" . rawurlencode($param);
            } else {
                $params[] = $key;
            }
        }

        return implode("&", $params);
    }
	
	/**
     * Sets accep encoding
     *
     * @param string
     */
    public static function setAcceptEncoding($encoding = "") {
       self::$requestAcceptEncoding = $encoding;
    }
	
}

<?php

namespace RESO;

use RESO\Error;
use RESO\Util;

abstract class Request
{
    private static $validOutputFormats = array("json", "xml");

    /**
     * Sends request and returns output in specified format.
     *
     * @param string $request
     * @param string $output_format
     * @param string $decode_json
     *
     * @return mixed API Request response in requested data format.
     */
    public static function request($request, $output_format = "xml", $decode_json = false)
    {
        // Get variables
        $api_request_url = \RESO\RESO::getAPIRequestUrl();
        $token = \RESO\RESO::getAccessToken();

        if(!in_array($output_format, self::$validOutputFormats)) {
            $output_format = "json";
        }

        $curl = new \RESO\HttpClient\CurlClient();

        // Build request URL
        $url = $api_request_url . $request;

        $headers = array(
            'Accept: application/json',
            'Authorization: Bearer '.$token
        );

        // Send request
        $response = $curl->request("get", $url, $headers, null, false);
        if(!$response || !is_array($response) || $response[1] != 200)
            throw new Error\Api("Could not retrieve API response. Request URL: ".$api_request_url."; Request string: ".$request."; Response: ".$response[0]);

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
}

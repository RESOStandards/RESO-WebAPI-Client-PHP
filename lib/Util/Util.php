<?php

namespace RESO\Util;

use RESO\Error;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isDomAvailable = null;
    private static $isXmlAvailable = null;

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }

    /**
     * @param string $url_string URL string with the code parameter.
     *
     * @return string Authentification code string.
     */
    public static function extractCode($url_string) {
        return explode("=", parse_url($url_string)["query"])[1];
    }

    /**
     * @param string $response_body HTML response with modelJson tag.
     *
     * @return array Extracted modelJson values in PHP array format.
     */
    public static function extractModelJson($response_body) {
        if (self::$isDomAvailable === null) {
            self::$isDomAvailable = extension_loaded("dom");

            if (!self::$isDomAvailable) {
                throw new Error\Reso("It looks like the DOM extension is not enabled. " .
                    "DOM extension is required to use the RESO API PHP SDK.");
            }
        }

        $doc = new \DOMDocument();
        @$doc->loadHTML($response_body);
        $extract = json_decode(htmlspecialchars_decode($doc->getElementById("modelJson")->textContent), true);
        return $extract;
    }

    /**
     * @param string $response_body HTML response with login form.
     *
     * @return array Form parameters and input field names and values.
     */
    public static function extractFormParameters($response_body) {
        $dom = new \DOMDocument();
        $returnArray = array();
        if(@$dom->loadHTML($response_body)) {
            $form = $dom->getelementsbytagname('form')[0];
            $returnArray["url"] = $form->getAttribute('action');
            $returnArray["method"] = $form->getAttribute('method');
            $returnArray["inputs"] = array();
            $inputs = $dom->getelementsbytagname('input');
            foreach ($inputs as $input) {
                $returnArray["inputs"][$input->getAttribute('name')] = $input->getAttribute('value');
            }
        }
        return $returnArray;
    }

    /**
     * @param array $arr A map of param keys to values.
     * @param string|null $prefix
     *
     * @return string A querystring, essentially.
     */
    public static function urlEncode($arr, $prefix = null)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        $r = array();
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if ($prefix) {
                if ($k !== null && (!is_int($k) || is_array($v))) {
                    $k = $prefix."[".$k."]";
                } else {
                    $k = $prefix."[]";
                }
            }

            if (is_array($v)) {
                $enc = self::urlEncode($v, $k);
                if ($enc) {
                    $r[] = $enc;
                }
            } else {
                $r[] = urlencode($k)."=".urlencode($v);
            }
        }

        return implode("&", $r);
    }

    /**
     * @param string $string
     *
     * @return bool True if the string is JSON, otherwise - False.
     */
    public static function isJson($string) {
        if(is_numeric($string)) return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param array $array
     *
     * @return string Returns XML formatted string.
     */
    public static function arrayToXml($array) {
        if (self::$isXmlAvailable === null) {
            self::$isXmlAvailable = function_exists('simplexml_load_file');

            if (!self::$isXmlAvailable) {
                throw new Error\Reso("It looks like the XML extension is not enabled. " .
                    "XML extension is required to use the RESO API PHP SDK, if the request response format is set to XML.");
            }
        }

        $xml = new \SimpleXMLElement('<root/>');
        self::_arrayToXml($array, $xml);
        return $xml->asXML();
    }

    /**
     * @param array $array
     * @param SimpleXMLElement &$xml
     *
     * @return bool True if the string is JSON, otherwise - False.
     */
    public static function _arrayToXml($array, &$xml) {
        foreach ($array as $key => $value) {
            if(is_array($value)){
                if(is_int($key)){
                    $key = "e";
                }
                $label = $xml->addChild($key);
                self::_arrayToXml($value, $label);
            }
            else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }
}

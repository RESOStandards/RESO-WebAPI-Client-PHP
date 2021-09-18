<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testRequestMetadata(): void
    {
        require('config.php');
        RESO\Reso::setClientId($client_id);
        RESO\Reso::setClientSecret($client_secret);
        RESO\Reso::setAPIAuthUrl($api_auth_url);
        RESO\Reso::setAPITokenUrl($api_token_url);
        RESO\Reso::setAPIRequestUrl($api_request_url);
        $auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);
        RESO\Reso::setAccessToken(RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope));
        if(stripos($api_request_url, "h.api.crmls.org") !== false) {
            RESO\Request::setAcceptType("json");
        }
        $xml = RESO\Request::requestMetadata();
        $this->assertTrue(!@simplexml_load_string($xml) === false);
    }

    public function testRequest(): void
    {
        require('config.php');
        RESO\Reso::setClientId($client_id);
        RESO\Reso::setClientSecret($client_secret);
        RESO\Reso::setAPIAuthUrl($api_auth_url);
        RESO\Reso::setAPITokenUrl($api_token_url);
        RESO\Reso::setAPIRequestUrl($api_request_url);
        $auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);
        RESO\Reso::setAccessToken(RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope));
        if(stripos($api_request_url, "h.api.crmls.org") !== false) {
            RESO\Request::setAcceptType("json");
        }
        $json = RESO\Request::request("Property?\$top=10", "json", false);
        $this->assertTrue(!@json_decode($json) === false);
        $array = json_decode($json, true);
        $this->assertTrue(is_array($array) && !empty($array));
    }
}

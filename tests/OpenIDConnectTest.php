<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpenIdConnectTest extends TestCase
{
    public function testAuthorize(): void
    {
        require('config.php');
        RESO\Reso::setClientId($client_id);
        RESO\Reso::setClientSecret($client_secret);
        RESO\Reso::setAPIAuthUrl($api_auth_url);
        RESO\Reso::setAPITokenUrl($api_token_url);
        RESO\Reso::setAPIRequestUrl($api_request_url);
        $auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);
        $this->assertNotNull($auth_code);
    }

    public function testRequestAccessToken(): void
    {
        require('config.php');
        RESO\Reso::setClientId($client_id);
        RESO\Reso::setClientSecret($client_secret);
        RESO\Reso::setAPIAuthUrl($api_auth_url);
        RESO\Reso::setAPITokenUrl($api_token_url);
        RESO\Reso::setAPIRequestUrl($api_request_url);
        $auth_code = RESO\OpenIDConnect::authorize($auth_username, $auth_password, $redirect_uri, $scope);
        $access_token = RESO\OpenIDConnect::requestAccessToken($auth_code, $redirect_uri, $scope);
        $this->access_token = $access_token;
        $this->assertNotNull($access_token);
    }

    public function testGetLoginUrl(): void
    {
        require('config.php');
        $this->assertTrue(!filter_var(RESO\OpenIDConnect::getLoginUrl($redirect_uri, $scope), FILTER_VALIDATE_URL) === false);
    }
}

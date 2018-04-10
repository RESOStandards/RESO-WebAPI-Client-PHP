# RESO API PHP SDK

The RESO API SDK for PHP allows developers to build applications with RESO API queries for listing data retrieval. More information at http://www.reso.org

## Requirements

PHP 5.3.3 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require reso/reso-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/reso-php-sdk/init.php');
```

## Dependencies

The following PHP extensions are required for all the RESO API SDK functions to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php)
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php)
- [`dom`](https://secure.php.net/manual/en/book.dom.php)

If you use Composer, these dependencies will be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started

Simple usage looks like:

```php
// Set the variables
RESO\RESO::setClientId('YOUR_CLIENT_ID');
RESO\RESO::setClientSecret('YOUR_CLIENT_SECRET');
RESO\RESO::setAPIAuthUrl('https://op.api.crmls.org/identity/connect/authorize');
RESO\RESO::setAPITokenUrl('https://op.api.crmls.org/identity/connect/token');
RESO\RESO::setAPIRequestUrl('https://h.api.crmls.org/RESO/OData/');
// Authorize user
$auth_code = RESO\OpenIDConnect::authorize('YOUR_USERNAME', 'YOUR_PASSWORD', 'https://openid.reso.org/', 'ODataApi');
// Get access token
RESO\RESO::setAccessToken(RESO\OpenIDConnect::requestAccessToken($auth_code, 'https://openid.reso.org/', 'ODataApi'));
// Set the Accept header (if needed)
RESO\Request::setAcceptType("json");
// Retrieve top 10 properties from the RESO API endpoint
$data = RESO\Request::request('Property?\$top=10', 'json', true);

// Display records
print_r($data);
```

## Example apps

Several usage examples are provided in the [`examples/`](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/tree/master/examples) folder:

- [`cli-example`](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/tree/master/examples/cli-example) - provides a sample console application to query RESO API data;
- [`web-example`](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/tree/master/examples/web-callback-example) - provides a sample PHP + HTML application to login (auth done on server-side) and execute RESO API requests, retrieve the data;
- [`web-callback-example`](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/tree/master/examples/web-example) - provides a sample PHP application, which demonstrates the user auth using callback URL.

To configure the example app variables / settings - copy the config.php file in each example application as _config.php and edit the variables accordingly.

## Configuring a Logger

The SDK has a built-in logger for debug / testing purposes. Usage:

```php
// Set logging
RESO\RESO::setLogEnabled(true); // enables logging in general. Default: false.
RESO\RESO::setLogConsole(true); // enables log messages to console.
RESO\RESO::setLogFile(true); // enabled log messages to be written to log file.

```

## Unit Tests

The SDK code set contains PHPUnit tests. The tests reside in the [`tests/`](https://github.com/RESO-RETS/RESOWebAPIReferenceClientinPHP/tree/master/tests) folder and covers core RESO PHP SDK functionality testing.

To run the tests duplicate the tests/config.php file to tests/_config.php and set the appropriate API variables. Then, execute:

```
./vendor/bin/phpunit --bootstrap init.php tests/
```
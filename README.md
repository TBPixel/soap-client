# PHP Soap Client

A WSDL supported SOAP client built on top of [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-18](https://www.php-fig.org/psr/psr-18/).


### Content

- [Installation](#installation)
- [Rational](#rational)
- [Examples](#examples)
- [Creating your own handler](#creating-your-own-handler)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [Support Me](#support-me)
- [License](#license)


## Installation

Note that this package is still _in-development_ and is subject to **breaking changes**.

You can install this package via composer:

```bash
composer require tbpixel/soap-client
```

*NOTE:* Version `0.2.0` pulled in Guzzle as the HTTP client of choice. Starting at `0.3.0`, the soap-client only relies on PSR-18. If you'd still like the convenience of using Guzzle without PSR-18 I'd recommend pulling version 0.2.0 with `composer require tbpixel/soap-client:^0.2.0`.


## Rational

If you (like me) have to work with SOAP in 2019, you may find yourself in a tight spot when working with _aggregate_ soap actions. If a particular soap action provides a lot of data, then not only will PHP's built-in soap client load the entire string response into memory but it will also instantiate a massive array of stdClass objects based on the response.

The above consumes a great deal of memory resources and can make it even more of a headache to deal with SOAP than it already is. This package aims to help with that by providing a PHP Soap Client built on top of PSR-18.

This, combined with another package I'm developing called [XML Streamer](https://github.com/TBPixel/xml-streamer), allows for some greatly improved memory management when working with soap API's.

## Examples

If you just want to get started quickly, the ClientFactory class will do most of the heavy lifting.

```php
use TBPixel\SoapClient\ClientFactory;

// @var \Psr\Http\Client\ClientInterface $http
$client = ClientFactory::new($http, 'http://example.com/service.wsdl');

// $response is a \Psr\Http\Message\StreamInterface
$response = $client->call('MyAction', [
    'foo' => 'bar',
]);
```


## Creating your own handler

By inspecting the ClientFactory class, you'll notice it does a lot of setup that gets quite tedious. The following steps are taken:

1. A soap client is created. This is used to get available wsdl functions and types, as well as to throw an exception when an invalid WSDL file is parsed.
2. A soap request formatter is created; this is what turns your action and body into a WSDL compatible soap request string.
3. A PSR-18 compatible handler is created.
4. A new Client is created and returned.

That's quite a lot of boilerplate. This is because it allows package internals to be extended and changed quickly while limiting the impact of package integrations.

Thankfully creating a client is easy. The `TBPixel\SoapClient\Handler` interface is ultimately what is passed to the client for handling requests. If PSR-18 doesn't suit your needs, you can just build your own client that implements the interface and pass it in instead!


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


### Support Me

Hi! I'm a developer living in Vancouver, BC and boy is the housing market tough. If you wanna support me, consider following me on [Twitter @TBPixel](https://twitter.com/TBPixel), or consider [buying me a coffee](https://ko-fi.com/tbpixel).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

# PHP Soap Client

A WSDL supported SOAP client built on top of guzzle.

### Content

- [Installation](#installation)
- [Rational](#rational)
- [Examples](#examples)
- [Creating your own client](#creating-your-own-client)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [Support Me](#support-me)
- [License](#license)

## Installation

Note that this package is still _in-development_ and is subject to **breaking changes**.

You can install this package via composer:

```bash
composer require tbpixel/soap-client:dev-master
```

## Rational

If you (like me) have to work with SOAP in 2019, you may find yourself in a tight spot when working with _aggregate_ soap actions. If a particular soap action provides a lot of data, then not only will PHP's built-in soap client load the entire string response into memory but it will also instantiate a massive array of stdClass objects based on the response.

The above consumes a great deal of memory resources and can make it even more of a headache to deal with SOAP than it already is. This package aims to help with that by providing a PHP Soap Client built on top of Guzzle. Under the hood, Guzzle (curl) will be making requests and returning responses as a stream. This allows us to be far more selective with our memory usage.

This, combined with another package I'm developing called [XML Streamer](https://github.com/TBPixel/xml-streamer), allows for some greatly improved memory management when working with soap API's.

## Examples

If you just want to get started quickly, the ClientFactory class will do most of the heavy lifting.

```php
use TBPixel\SoapClient\ClientFactory;

$wsdl = 'http://example.com/service.wsdl';
$client = ClientFactory::new($wsdl);

// $response is a \Psr\Http\Message\StreamInterface
$response = $client->call('MyAction', [
    'foo' => 'bar',
]);
```

## Creating your own client

By inspecting the ClientFactory class, you'll notice it does a lot of setup that gets quite tedius. The following steps are taken:

1. A soap client is created. This is used to get available wsdl functions and types, as well as to throw an exception when an invalid WSDL file is parsed.
2. A new guzzle client is created.
3. A soap request formatter is created; this is what turns your action and body into a WSDL compatible soap request string.
4. A Guzzle handler is created; this is then passed in the client, formatter and uri of the soap api.
5. A new Client is created, given everything it needs and returned.

```php
use GuzzleHttp\Client as Guzzle;
use TBPixel\SoapClient\Handlers\GuzzleHandler;
use TBPixel\SoapClient\Formatters\SoapRequestFormatter;

/**
 * A factory function to help build up a soap client quickly.
 *
 * Will automatically create a new SoapClient for use with the Client.
 */
final class ClientFactory
{
    public static function new(string $wsdl, array $soapOptions = []): Client
    {
        $soap = new \SoapClient($wsdl, $soapOptions);
        $guzzle = new Guzzle;
        $formatter = new SoapRequestFormatter($wsdl, $soapOptions);
        $handler = new GuzzleHandler($guzzle, $formatter, $wsdl);

        return new Client($wsdl, $soap, $handler);
    }
}
```

That's quite a lot of boilerplate. This is because it allows package internals to be extended and changed quickly while limiting the impact of package integrators.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Support Me

Hi! I'm a developer living in Vancouver, BC and boy is the housing market tough. If you wanna support me, consider following me on [Twitter @TBPixel](https://twitter.com/TBPixel), or consider [buying me a coffee](https://ko-fi.com/tbpixel).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

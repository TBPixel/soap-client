<?php

namespace TBPixel\SoapClient;

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

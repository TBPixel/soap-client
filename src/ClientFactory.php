<?php

namespace TBPixel\SoapClient;

use Psr\Http\Client\ClientInterface;
use TBPixel\SoapClient\Handlers\PsrHandler;
use TBPixel\SoapClient\Formatters\SoapRequestFormatter;

/**
 * A factory function to help build up a soap client quickly.
 *
 * Will automatically create a new SoapClient for use with the Client.
 */
final class ClientFactory
{
    public static function new(ClientInterface $client, string $wsdl, array $soapOptions = []): Client
    {
        $soap = new \SoapClient($wsdl, $soapOptions);
        $formatter = new SoapRequestFormatter($wsdl, $soapOptions);
        $handler = new PsrHandler($client, $formatter);

        return new Client($wsdl, $soap, $handler);
    }
}

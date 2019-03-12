<?php

namespace TBPixel\SoapClient\Handlers;

use GuzzleHttp\Psr7\Request;
use TBPixel\SoapClient\Handler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;

/**
 * A PSR-15 compatible implementation of the soap request Handler.
 */
final class PsrHandler implements Handler
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * @var \TBPixel\SoapClient\Handlers\Formatter
     */
    private $formatter;

    /**
     * @var string
     */
    private $uri;

    public function __construct(ClientInterface $client, Formatter $formatter)
    {
        $this->client = $client;
        $this->formatter = $formatter;
    }

    public function request(string $action, array $body): StreamInterface
    {
        try {
            $soapCall = $this->formatter->format($action, $body);
            $request = $this->makeRequest('POST', $soapCall);

            $response = $this->client->sendRequest($request);

            return $response->getBody();
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }

    private function makeRequest(string $method, SoapCall $soapCall): RequestInterface
    {
        return new Request(
            $method,
            $soapCall->getLocation(),
            [
                'content-type' => 'text/xml',
                'SOAPAction' => $soapCall->getAction(),
            ],
            $soapCall->getBody(),
            );
    }
}

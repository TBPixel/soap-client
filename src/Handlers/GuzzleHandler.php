<?php

namespace TBPixel\SoapClient\Handlers;

use TBPixel\SoapClient\Handler;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A Guzzle implementation of the soap request Handler.
 */
final class GuzzleHandler implements Handler
{
    /**
     * The Guzzle client.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * The XML Request formatter
     *
     * @var \TBPixel\SoapClient\Handlers\Formatter
     */
    private $formatter;

    public function __construct(ClientInterface $client, Formatter $formatter)
    {
        $this->client = $client;
        $this->formatter = $formatter;
    }

    public function request(string $action, array $body): StreamInterface
    {
        try {
            $soapCall = $this->formatter->format($action, $body);
            $response = $this->client->request('POST', $soapCall->getLocation(), [
                'headers' => [
                    'content-type' => 'text/xml',
                    'SOAPAction' => $soapCall->getAction(),
                ],
                'body' => $soapCall->getBody(),
            ]);

            return $response->getBody();
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }
}

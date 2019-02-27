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

    /**
     * The service endpoint to make a request to.
     *
     * @var string
     */
    private $uri;

    public function __construct(ClientInterface $client, Formatter $formatter, string $uri)
    {
        $this->client = $client;
        $this->formatter = $formatter;
        $this->uri = $uri;
    }

    public function request(string $action, array $body): StreamInterface
    {
        try {
            $response = $this->client->request('POST', $this->uri, [
                'headers' => [
                    'content-type' => 'text/xml',
                    'SOAPAction' => $action,
                ],
                'body' => $this->formatter->format($action, $body),
            ]);

            return $response->getBody();
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }
}

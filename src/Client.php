<?php

namespace TBPixel\SoapClient;

use Psr\Http\Message\StreamInterface;

/**
 * The Soap Client.
 *
 * This client is responsible for making requests to a SOAP API.
 */
class Client
{
    /**
     * The HTTP request handler.
     *
     * @var \TBPixel\SoapClient\Handler
     */
    private $handler;

    /**
     * The WSDL service location.
     *
     * @var string
     */
    private $wsdl;

    /**
     * An array of actions resolved from the WSDL.
     *
     * @var array
     */
    private $functions;

    /**
     * An array of types resolved from the WSDL.
     *
     * @var array
     */
    private $types;

    /**
     * A SOAP client connection for basic parsing.
     *
     * @var \SoapClient
     */
    private $soap;

    /**
     * Construct a new soap client.
     */
    public function __construct(string $wsdl, \SoapClient $connection, Handler $handler)
    {
        $this->wsdl      = $wsdl;
        $this->handler   = $handler;
        $this->soap      = $connection;
        $this->functions = $this->soap->__getFunctions();
        $this->types     = $this->soap->__getTypes();
    }

    /**
     * Execute a SOAP function call.
     *
     * @throws \RuntimeException
     */
    public function call(string $name, array $args = []): StreamInterface
    {
        return $this->handler->request($name, $args);
    }

    /**
     * Retreives the list of registered functions for this client.
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Retrieves the list of registered types for this client.
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}

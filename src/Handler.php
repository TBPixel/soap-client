<?php

namespace TBPixel\SoapClient;

use Psr\Http\Message\StreamInterface;

/**
 * A handler interface for SOAP requests.
 *
 * A handler must provided a request method which calls a SOAP Action; it must then return the response.
 */
interface Handler
{
    /**
     * Executes a SOAP Action with body, returning the response, if any.
     *
     * @throws \RuntimeException
     */
    public function request(string $action, array $body): StreamInterface;
}

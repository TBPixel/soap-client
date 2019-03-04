<?php

namespace TBPixel\SoapClient\Formatters;

use TBPixel\SoapClient\Handlers\Formatter;
use TBPixel\SoapClient\Exceptions\RequestFormatterHack;

/**
 * Handles the formatting of a an XML soap request.
 */
final class SoapRequestFormatter extends \SoapClient implements Formatter
{
    /**
     * @var string
     */
    private $wsdl;

    public function __construct(string $wsdl, array $soapOptions = [])
    {
        parent::__construct($wsdl, $soapOptions);
        $this->wsdl = $wsdl;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        // NOTE!
        // This is a MASSIVE hack. It is very important that a formatter be rewritten,
        // or a new one provided, to ensure formatting of requests is handled correctly.
        // Invalid requests will cause weird bugs because of this process.
        throw new RequestFormatterHack($request);
    }

    public function format(string $action, array $body): string
    {
        try {
            $this->__soapCall($action, $body);
            // NOTE!
            // See __doRequest comment for info about this hack.
        } catch (RequestFormatterHack $hack) {
            return $hack->getMessage();
        }
    }
}

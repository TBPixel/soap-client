<?php

namespace TBPixel\SoapClient\Exceptions;

use TBPixel\SoapClient\Handlers\SoapCall;

final class RequestFormatterHack extends \Exception
{
    /** @var \TBPixel\SoapClient\Handlers\SoapCall */
    private $soapCall;

    public function __construct(SoapCall $soapCall)
    {
        parent::__construct();
        $this->soapCall = $soapCall;
    }

    public function getSoapCall(): SoapCall
    {
        return $this->soapCall;
    }
}

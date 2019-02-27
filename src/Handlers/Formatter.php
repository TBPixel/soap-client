<?php

namespace TBPixel\SoapClient\Handlers;

/**
 * Contracts implementors to accept an array of structured data and format it as a string body.
 */
interface Formatter
{
    /**
     * Accepts an array of data and formats it in the appropriate request structure.
     */
    public function format(string $action, array $body): string;
}

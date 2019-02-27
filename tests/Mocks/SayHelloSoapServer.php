<?php

namespace TBPixel\SoapClient\Tests\Mocks;

final class SayHelloSoapServer
{
    public function SayHelloTo($person): array
    {
        return [
            'Message' => "Hello, {$person->FirstName} {$person->LastName}",
        ];
    }
}

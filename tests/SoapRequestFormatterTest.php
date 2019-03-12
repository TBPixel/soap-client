<?php

namespace TBPixel\SoapClient\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\SoapClient\Formatters\SoapRequestFormatter;

final class SoapRequestFormatterTest extends TestCase
{
    /**
     * @var \TBPixel\SoapClient\Formatters\SoapRequestFormatter
     */
    private $formatter;

    protected function setUp(): void
    {
        $this->formatter = new SoapRequestFormatter(__DIR__ . '/data/SayHello.wsdl');
    }

    /** @test */
    public function can_format_request()
    {
        $request = $this->formatter->format('SayHelloTo', [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ]
        ]);

        $person = file_get_contents(__DIR__ . '/data/SayHelloPerson.xml');
        $expected = trim(preg_replace('/\s+/', '', $person));
        $actual = trim(preg_replace('/\s+/', '', $request->getBody()));

        $this->assertEquals('SayHelloTo', $request->getAction());
        $this->assertEquals('http://localhost/SayHello/', $request->getLocation());
        $this->assertEquals($expected, $actual);
    }
}

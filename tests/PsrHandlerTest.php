<?php

namespace TBPixel\SoapClient\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use TBPixel\SoapClient\Handlers\PsrHandler;
use TBPixel\SoapClient\Formatters\SoapRequestFormatter;

final class PsrHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private $wsdl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wsdl = __DIR__ . '/data/SayHello.wsdl';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function failed_request_will_throw_runtime_exception()
    {
        $this->expectException(\RuntimeException::class);

        $mock = Mockery::mock(ClientInterface::class)
            ->shouldReceive('sendRequest')
            ->andThrow(\Exception::class)
            ->getMock();

        $formatter = new SoapRequestFormatter($this->wsdl);
        $handler = new PsrHandler($mock, $formatter, $this->wsdl);

        $handler->request('SayHelloTo', [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ]
        ]);
    }
}

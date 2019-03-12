<?php

namespace TBPixel\SoapClient\Tests;

use Mockery;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use TBPixel\SoapClient\Handlers\SoapCall;
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

    /**
     * Mocks and returns a PSR compatible failed request which throws an exception.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    private function mockClientFailedRequest()
    {
        return Mockery::mock(ClientInterface::class)
            ->shouldReceive('sendRequest')
            ->andThrow(\Exception::class)
            ->getMock();
    }

    /**
     * Mocks and returns a PSR compatible successful request.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    private function mockClientSuccessfulRequest(SoapCall $soapCall, string $result)
    {
        $stream = Mockery::mock(StreamInterface::class)
            ->shouldReceive('getContents')
            ->andReturn($result)
            ->getMock();

        $response = Mockery::mock(ResponseInterface::class)
            ->shouldReceive('getBody')
            ->andReturn($stream)
            ->getMock();

        return Mockery::mock(ClientInterface::class)
            ->shouldReceive('sendRequest')
            ->andReturn($response)
            ->getMock();
    }

    /** @test */
    public function failed_request_will_throw_runtime_exception()
    {
        $this->expectException(\RuntimeException::class);

        $mock = $this->mockClientFailedRequest();

        $formatter = new SoapRequestFormatter($this->wsdl);
        $handler = new PsrHandler($mock, $formatter);

        $handler->request('SayHelloTo', [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ]
        ]);
    }

    /** @test */
    public function successful_request_should_return_valid_response()
    {
        $action = 'SayHelloTo';
        $body = [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ],
        ];
        $result = 'success';

        $formatter = new SoapRequestFormatter($this->wsdl);
        $soapCall = $formatter->format($action, $body);
        $mock = $this->mockClientSuccessfulRequest($soapCall, $result);

        $handler = new PsrHandler($mock, $formatter);
        $response = $handler->request($action, $body);

        $this->assertEquals($result, $response->getContents());
    }
}

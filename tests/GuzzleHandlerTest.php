<?php

namespace TBPixel\SoapClient\Tests;

use Mockery;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use TBPixel\SoapClient\Handlers\SoapCall;
use TBPixel\SoapClient\Handlers\GuzzleHandler;
use TBPixel\SoapClient\Formatters\SoapRequestFormatter;

final class GuzzleHandlerTest extends TestCase
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
     * Mocks and returns a failed guzzle request which throws an exception.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    private function mockGuzzleRequestException()
    {
        return Mockery::mock(ClientInterface::class)
            ->shouldReceive('request')
            ->andThrow(\Exception::class)
            ->getMock();
    }

    /**
     * Mocks and returns a successful guzzle request which returns as expected.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    private function mockGuzzleSuccessfulHandler(SoapCall $soapCall, string $result)
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
            ->shouldReceive('request')
            ->with('POST', $soapCall->getLocation(), [
                'headers' => [
                    'content-type' => 'text/xml',
                    'SOAPAction' => $soapCall->getAction(),
                ],
                'body' => $soapCall->getBody(),
            ])
            ->andReturn($response)
            ->getMock();
    }

    /** @test */
    public function failed_request_will_throw_runtime_exception()
    {
        $this->expectException(\RuntimeException::class);

        $mock = $this->mockGuzzleRequestException();

        $formatter = new SoapRequestFormatter($this->wsdl);
        $handler = new GuzzleHandler($mock, $formatter, $this->wsdl);

        $handler->request('SayHelloTo', [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ]
        ]);
    }

    /** @test */
    public function should_return_valid_response()
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
        $mock = $this->mockGuzzleSuccessfulHandler($soapCall, $result);

        $handler = new GuzzleHandler($mock, $formatter);
        $response = $handler->request($action, $body);

        $this->assertEquals($result, $response->getContents());
    }
}

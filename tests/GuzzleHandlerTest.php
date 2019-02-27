<?php

namespace TBPixel\SoapClient\Tests;

use Mockery;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
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

    /** @test */
    public function failed_request_will_throw_runtime_exception()
    {
        $this->expectException(\RuntimeException::class);

        $mock = Mockery::mock(ClientInterface::class)
            ->shouldReceive('request')
            ->andThrow(\Exception::class)
            ->getMock();

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

        $stream = Mockery::mock(StreamInterface::class)
            ->shouldReceive('getContents')
            ->andReturn($result)
            ->getMock();

        $response = Mockery::mock(ResponseInterface::class)
            ->shouldReceive('getBody')
            ->andReturn($stream)
            ->getMock();

        $formatter = new SoapRequestFormatter($this->wsdl);

        $mock = Mockery::mock(ClientInterface::class)
            ->shouldReceive('request')
            ->with('POST', $this->wsdl, [
                'headers' => [
                    'content-type' => 'text/xml',
                    'SOAPAction' => $action,
                ],
                'body' => $formatter->format($action, $body),
            ])
            ->andReturn($response)
            ->getMock();

        $handler = new GuzzleHandler($mock, $formatter, $this->wsdl);
        $response = $handler->request($action, $body);

        $this->assertEquals($result, $response->getContents());
    }
}

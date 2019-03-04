<?php declare(strict_types = 1);

namespace TBPixel\SoapClient\Tests;

use TBPixel\SoapClient\Client;
use TBPixel\SoapClient\Handler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use TBPixel\SoapClient\ClientFactory;
use TBPixel\SoapClient\Tests\Mocks\HandlerMock;
use TBPixel\SoapClient\Tests\Mocks\SayHelloHandler;

final class ClientTest extends TestCase
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

    private function makeTestClient(Handler $handler): Client
    {
        $soap = new \SoapClient($this->wsdl, [
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);

        return new Client($this->wsdl, $soap, $handler);
    }

    /** @test */
    public function can_parse_wsdl()
    {
        $handler = new HandlerMock;
        $client = $this->makeTestClient($handler);

        $this->assertEquals(['Greeting SayHelloTo(Person $body)'], $client->getFunctions());

        $structs = array_map(
            function (string $struct) {
                $match = preg_replace('/\s+/', ' ', $struct);

                if ($match) {
                    return trim($match);
                }
            },
            $client->getTypes()
        );

        $this->assertEquals([
            'struct Person { string FirstName; string LastName; }',
            'struct Greeting { string Message; }',
        ], $structs);
    }

    /** @test */
    public function can_call_action()
    {
        $handler = new HandlerMock;
        $client = $this->makeTestClient($handler);

        $result = $client->call('SayHelloTo');

        $this->assertNotNull($result);
        $this->assertInstanceOf(StreamInterface::class, $result);

        $result->close();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function can_get_valid_soap_response()
    {
        $handler = new SayHelloHandler($this->wsdl, ['trace' => true]);
        $client = $this->makeTestClient($handler);

        $response = $client->call('SayHelloTo', [
            'Person' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
            ],
        ]);

        $greeting = file_get_contents(__DIR__ . '/data/SayHelloGreeting.xml');
        $expected = trim(preg_replace('/\s+/', '', $greeting));
        $actual = trim(preg_replace('/\s+/', '', $response->getContents()));

        $this->assertEquals($expected, $actual);
    }
}

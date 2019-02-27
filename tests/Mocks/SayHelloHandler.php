<?php declare(strict_types = 1);

namespace TBPixel\SoapClient\Tests\Mocks;

use GuzzleHttp\Psr7\Stream;
use TBPixel\SoapClient\Handler;
use Psr\Http\Message\StreamInterface;

final class SayHelloHandler extends \SoapClient implements Handler
{
    /**
     * @var \SoapServer
     */
    private $server;

    public function __construct(string $wsdl, array $options = [])
    {
        parent::__construct($wsdl, $options);

        $this->server = new \SoapServer($wsdl, $options);
        $this->server->setClass(SayHelloSoapServer::class);
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        ob_start();
        $this->server->handle($request);
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }

    public function request(string $action, array $body): StreamInterface
    {
        $this->__soapCall($action, $body);

        return new Stream(
            $this->stringToStream($this->__getLastResponse())
        );
    }

    /**
     * @return resource
     */
    private function stringToStream(string $string)
    {
        $stream = fopen('php://memory', 'r+');

        if (!$stream) {
            throw new \RuntimeException('could not open in-memory stream!');
        }

        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }
}

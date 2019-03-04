<?php declare(strict_types = 1);

namespace TBPixel\SoapClient\Tests\Mocks;

use GuzzleHttp\Psr7\Stream;
use TBPixel\SoapClient\Handler;
use Psr\Http\Message\StreamInterface;

final class HandlerMock implements Handler
{
    public function request(string $action, array $body): StreamInterface
    {
        $stream = fopen('php://memory', 'r+');

        if (!$stream) {
            throw new \RuntimeException('could not open in-memory stream!');
        }

        $request = implode('|', $body);

        fwrite($stream, "{$action}: {$request}");
        rewind($stream);

        return new Stream($stream);
    }
}

<?php

namespace TBPixel\SoapClient\Handlers;

final class SoapCall
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $body;

    public function __construct(string $action, string $location, string $body)
    {
        $this->action = $action;
        $this->location = $location;
        $this->body = $body;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}

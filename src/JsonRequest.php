<?php

namespace Diciotto;

class JsonRequest extends Request
{
    public function __construct(string $uri, string $method = 'GET', array $body = null)
    {
        parent::__construct($uri, $method, json_encode($body));
        $this->psrRequest = $this->psrRequest->withHeader('Content-Type', 'application/json');
        $this->psrRequest = $this->psrRequest->withHeader('Accept', 'application/json');
    }
}

<?php

namespace Diciotto;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    protected $psrRequest;

    public function __construct($uri, $method = 'GET', $body = null) {
        $this->psrRequest = new \Nyholm\Psr7\Request($method, $uri, [], $body);
    }

    // proxy methods for \Nyholm\Psr7\Request

    public function getProtocolVersion() : string
    {
        return $this->psrRequest->getProtocolVersion();
    }

    public function withProtocolVersion($version) : self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withProtocolVersion($version);
        return $new;
    }

    public function getHeaders() : array
    {
        return $this->psrRequest->getHeaders();
    }

    public function hasHeader($name) : bool
    {
        return $this->psrRequest->hasHeader($name);
    }

    public function getHeader($name) : array
    {
        return $this->psrRequest->getHeader($name);
    }

    public function getHeaderLine($name) : string
    {
        return $this->psrRequest->getHeaderLine($name);
    }

    public function withHeader($name, $value) : self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withHeader($name, $value);
        return $new;
    }

    public function withAddedHeader($name, $value) : self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withAddedHeader($name, $value);
        return $new;
    }

    public function withoutHeader($name): self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withoutHeader($name);
        return $new;
    }

    public function getBody() : StreamInterface
    {
        return $this->psrRequest->getBody();
    }

    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withBody($body);
        return $new;
    }

    public function getRequestTarget() : string
    {
        return $this->psrRequest->getRequestTarget();
    }

    public function withRequestTarget($requestTarget): self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withRequestTarget($requestTarget);
        return $new;
    }

    public function getMethod() : string
    {
        return $this->psrRequest->getMethod();
    }

    public function withMethod($method) : self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withMethod($method);
        return $new;
    }

    public function getUri() : UriInterface
    {
        return $this->psrRequest->getUri();
    }

    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $new = clone $this;
        $new->psrRequest = $this->psrRequest->withUri($uri, $preserveHost);
        return $new;
    }
}
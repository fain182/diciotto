<?php

namespace Tests\Diciotto\Unit;

use Diciotto\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSetSingleCookie(): void
    {
        $request = new Request('http://www.example.com');

        $request = $request->withCookie('name', 'value');

        $this->assertEquals('name=value', $request->getHeaderLine('Cookie'));
    }

    public function testAddSingleCookie(): void
    {
        $request = new Request('http://www.example.com');

        $request = $request->withAddedCookie('name', 'value');

        $this->assertEquals('name=value', $request->getHeaderLine('Cookie'));
    }

    public function testAddMultipleCookies(): void
    {
        $request = new Request('http://www.example.com');

        $request = $request->withAddedCookie('name', 'value');
        $request = $request->withAddedCookie('foo', 'bar');

        $this->assertEquals('name=value; foo=bar', $request->getHeaderLine('Cookie'));
    }
}

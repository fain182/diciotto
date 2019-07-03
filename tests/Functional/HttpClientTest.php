<?php

namespace Tests\Diciotto\Functional;

use Diciotto\HttpClient;
use Diciotto\JsonRequest;
use Diciotto\Request;

class HttpClientTest extends \PHPUnit\Framework\TestCase
{
    public function testGet(): void
    {
        $client = new HttpClient();
        $request = new Request('https://ideato.it/robots.txt');

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith('User-agent:', (string) $response->getBody());
        $this->assertContains($response->getProtocolVersion(), ['1.1', '2']);
        $this->assertEquals(['text/plain; charset=utf-8'], $response->getHeader('content-type'));
    }

    public function testPostSendFormData()
    {
        $client = new HttpClient();
        $dataToSend = ['abc' => 'def'];
        $request = new Request('https://httpbin.org/post', 'POST', http_build_query($dataToSend));
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals($dataToSend, $body['form']);
    }

    public function testCookie(): void
    {
        $client = new HttpClient();
        $request = new Request('https://httpbin.org/get');
        $request = $request->withAddedCookie('name', 'value');

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $cookieSent = $body['headers']['Cookie'];
        $this->assertEquals('name=value', $cookieSent);
    }

    public function testMultipleCookies(): void
    {
        $client = new HttpClient();
        $request = new Request('https://httpbin.org/get');
        $request = $request->withAddedCookie('name', 'value');
        $request = $request->withAddedCookie('foo', 'bar');

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $cookieSent = $body['headers']['Cookie'];
        $this->assertEquals('name=value; foo=bar', $cookieSent);
    }

    public function testPutSendData()
    {
        $client = new HttpClient();
        $dataToSend = ['abc' => 'def'];
        $request = new JsonRequest('https://httpbin.org/put', 'PUT', $dataToSend);
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $dataSent = json_decode($body['data'], true);
        $this->assertEquals($dataToSend, $dataSent);
    }

    public function testItFollowsRedirect(): void
    {
        $client = new HttpClient();
        $request = new Request('http://httpbin.org/redirect-to?url=http%3A%2F%2Fwww.google.it%2Frobots.txt&status_code=301');

        $response = $client->sendRequest($request);

        $this->assertStringStartsWith('User-agent:', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}

<?php

namespace Tests\Diciotto\Functional;

use Diciotto\HttpClient;
use Diciotto\JsonRequest;
use Diciotto\Request;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientTest extends \PHPUnit\Framework\TestCase
{

    public function testPageNotFound() : void {
        $client = new HttpClient();
        $request = new Request('http://www.google.com/DOES/NOT/EXISTS');

        $response = $client->sendRequest($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('<title>Error 404 (Not Found)', (string) $response->getBody());
    }

    public function testHostNotFound() : void {
        $this->expectException(NetworkExceptionInterface::class);
        $this->expectExceptionMessage("Could not resolve host: www.does.not.exists");

        $client = new HttpClient();
        $request = new Request('http://www.does.not.exists');

        $client->sendRequest($request);
    }

    public function testGet() : void {
        $client = new HttpClient();
        $request = new Request('https://www.google.com/robots.txt');

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith('User-agent:', (string) $response->getBody());
        $this->assertEquals(1.1, $response->getProtocolVersion());
        $this->assertEquals(['text/plain'], $response->getHeader('content-type'));
    }

    public function testPutSendData() {
        $client = new HttpClient();
        $dataToSend = ['abc' => 'def'];
        $request = new JsonRequest('https://httpbin.org/put', 'PUT', $dataToSend);
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($dataToSend, $this->parseBodyFromHttpBinResponse($response));
    }

    private function parseBodyFromHttpBinResponse(ResponseInterface $response)
    {
        $body = json_decode($response->getBody(), true);
        return json_decode($body['data'], true);
    }

}

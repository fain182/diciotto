<?php

namespace Tests\Diciotto\Functional;

use Diciotto\HttpClient;
use Diciotto\JsonRequest;
use Diciotto\Request;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientErrorTest extends \PHPUnit\Framework\TestCase
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

    public function testInvalidMethod() : void {
        $this->expectException(RequestExceptionInterface::class);
        $this->expectExceptionMessage("Unknown HTTP method: 'ASD'");

        $client = new HttpClient();
        $request = new Request('http://www.google.it', 'ASD');

        $client->sendRequest($request);
    }

}

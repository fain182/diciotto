<?php

namespace Tests\Diciotto\Functional;

use Diciotto\HttpClient;
use Diciotto\Request;
use Psr\Http\Client\NetworkExceptionInterface;

class HttpClientTimeoutTest extends \PHPUnit\Framework\TestCase
{
    public function testExpiredTimeout(): void
    {
        $this->expectException(NetworkExceptionInterface::class);
        $this->expectExceptionMessageRegExp("/Operation timed out after \d+ milliseconds with 0 bytes received/");

        $client = (new HttpClient())->withTimeout(2);
        $request = new Request('http://slowwly.robertomurray.co.uk/delay/10000/url/http://www.example.com');

        $client->sendRequest($request);
    }

    public function testNotExpiredTimeout(): void
    {
        $client = (new HttpClient())->withTimeout(20);
        $request = new Request('https://www.google.com/robots.txt');

        $response = $client->sendRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

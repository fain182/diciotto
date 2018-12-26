<?php

namespace Tests\Diciotto\Functional;

use Diciotto\HttpClient;
use Diciotto\Request;
use Psr\Http\Client\NetworkExceptionInterface;


class HttpClientSslTest extends \PHPUnit\Framework\TestCase
{

    public function testSelfSignedCertificate() : void {
        $this->expectException(NetworkExceptionInterface::class);
        $this->expectExceptionMessage("SSL certificate problem: self signed certificate");

        $client = (new HttpClient());
        $request = new Request('https://self-signed.badssl.com/');

        $client->sendRequest($request);
    }

    public function testIgnoreCertificateErrors() : void {
        $client = (new HttpClient())->withCheckSslCertificates(false);
        $request = new Request('https://self-signed.badssl.com/');

        $response = $client->sendRequest($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

}

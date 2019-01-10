<?php

namespace Diciotto;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface
{
    private $timeoutInSeconds = 15;
    private $checkSslCertificate = true;

    public function withTimeout(int $timeoutInSeconds): self
    {
        $this->timeoutInSeconds = $timeoutInSeconds;

        return $this;
    }

    public function withCheckSslCertificates(bool $checkSslCertificate): self
    {
        $this->checkSslCertificate = $checkSslCertificate;

        return $this;
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface if an error happens while processing the request
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $curl = CurlHandleFactory::build($request);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeoutInSeconds);

        if (false === $this->checkSslCertificate) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $headerLines = [];
        curl_setopt(
            $curl,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $headerLine) use (&$headerLines) {
                $len = strlen($headerLine);
                $headerLines[] = $headerLine;

                return $len;
            }
        );

        $responseBody = curl_exec($curl);
        if (false === $responseBody) {
            throw new NetworkException(curl_error($curl), $request);
        }
        curl_close($curl);

        $headerLines = $this->discardRedirectsHeaders($headerLines);
        $version = $this->parseHttpVersion($headerLines);
        $statusCode = $this->parseStatusCode($headerLines);
        $headers = $this->parseHttpHeaders($headerLines);

        return new Response($statusCode, $headers, $responseBody, $version);
    }

    private function parseHttpVersion($headerLines): string
    {
        preg_match('/http\/(.+) (\d+) /i', $headerLines[0], $matches);

        return $matches[1];
    }

    private function parseStatusCode($headerLines): int
    {
        preg_match('/http\/(.+) (\d+) /i', $headerLines[0], $matches);

        return $matches[2];
    }

    private function parseHttpHeaders($headerLines): array
    {
        array_shift($headerLines);

        $headers = [];
        foreach ($headerLines as $header) {
            $header = explode(':', $header, 2);
            if (count($header) >= 2) {
                $headers[strtolower(trim($header[0]))] = trim($header[1]);
            }
        }

        return $headers;
    }

    /*
     * When curl follow redirects give us the headers of all the request instead of only the last one,
     * so we need to select the headers only for the last request.
     */
    private function discardRedirectsHeaders($headerLines): array
    {
        $lastHttpRequestStartAtIndex = 0;
        for ($i = 0; $i < count($headerLines); ++$i) {
            if (preg_match('/http\/(.+) (\d+) /i', $headerLines[$i], $matches)) {
                $lastHttpRequestStartAtIndex = $i;
            }
        }

        return array_slice($headerLines, $lastHttpRequestStartAtIndex);
    }
}

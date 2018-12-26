<?php

namespace Diciotto;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface
{
    private $timeoutInSeconds = 15;

    public function withTimeout(int $timeoutInSeconds) : self
    {
        $this->timeoutInSeconds = $timeoutInSeconds;
        return $this;
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $curl = CurlHandleFactory::build($request);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeoutInSeconds);

        $headerLines = [];
        curl_setopt(
            $curl,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $headerLine) use (&$headerLines) {
                $len = strlen($headerLine);
                $headerLines []= $headerLine;
                return $len;
            }
        );

        $responseBody = curl_exec($curl);
        if ($responseBody === false) {
            throw new NetworkException(curl_error($curl), $request);
        }

        $version = $this->parseHttpVersion($headerLines);
        $statusCode = $this->parseStatusCode($headerLines);
        $headers = $this->parseHttpHeaders($headerLines);

        return new Response($statusCode, $headers, $responseBody, $version);
    }

    private function parseHttpVersion($headerLines): string {
        preg_match('/http\/(.+) (\d+) /i', $headerLines[0], $matches);
        return $matches[1];
    }

    private function parseStatusCode($headerLines): int {
        preg_match('/http\/(.+) (\d+) /i', $headerLines[0], $matches);
        return $matches[2];
    }

    private function parseHttpHeaders($headerLines): array {
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

}
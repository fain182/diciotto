<?php

namespace Diciotto;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface
{
    private $debugEnabled = false;

    public function enableDebug(bool $debugEnabled) {
        $this->debugEnabled = $debugEnabled;
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
        if ($this->debugEnabled) echo $request->getUri()."\n";
        $curl = curl_init();
        if ($this->debugEnabled) curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        curl_setopt($curl,CURLOPT_URL, $request->getUri());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $this->setupHttpMethodAndBody($request, $curl);

        $this->setupRequestHeader($request, $curl);


        list($headers, $version, $responseBody) = $this->executeRequest($curl);

        if ($this->debugEnabled) echo curl_getinfo($curl, CURLINFO_HEADER_OUT);

        if ($responseBody === false) {
            throw new NetworkException(curl_error($curl), $request);
        }
        $statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        return new Response($statusCode, $headers, $responseBody, $version);
    }

    function startsWith($haystack, $needle) : bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private function setupHttpMethodAndBody(RequestInterface $request, $curl): void
    {
        switch ($request->getMethod()) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
                break;
            case 'GET':
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
            default:
                throw new RequestException('Method "'.$request->getMethod().'" is not valid.');
        }
    }

    private function setupRequestHeader(RequestInterface $request, $curl): void
    {
        $headerLines = [];
        foreach ($request->getHeaders() as $headerName => $values) {
            $headerLines[] = $headerName.": ".$request->getHeaderLine($headerName);
        }
        if ($this->debugEnabled) {
            echo "HEADERS:";
            var_dump($headerLines);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerLines);
    }

    private function executeRequest($curl): array
    {
        $headers = [];
        $version = '';
        curl_setopt(
            $curl,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers, &$version) {
                $len = strlen($header);

                if ($this->startsWith($header, 'HTTP/')) {
                    $version = substr($header, 5, 3);
                } else {
                    $header = explode(':', $header, 2);
                    if (count($header) >= 2) {
                        $headers[strtolower(trim($header[0]))] = trim($header[1]);
                    }
                }

                return $len;
            }
        );

        $responseBody = curl_exec($curl);

        return array($headers, $version, $responseBody);
    }

}
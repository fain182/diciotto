<?php

namespace Diciotto;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements ClientInterface
{

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
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $request->getUri());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [];
        $version = '';
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers, &$version) {
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
        });

        $responseBody = curl_exec($curl);
        if ($responseBody === false) {
            throw new NetworkException(curl_error($curl), $request);
        }
        $statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        return new Response($statusCode, $headers, $responseBody, $version);
    }

    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

}
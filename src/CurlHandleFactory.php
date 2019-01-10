<?php

namespace Diciotto;

use Psr\Http\Message\RequestInterface;

class CurlHandleFactory
{
    public static function build(RequestInterface $request)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $request->getUri());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        switch ($request->getMethod()) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string) $request->getBody());
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
            default:
                throw new RequestException("Unknown HTTP method: '{$request->getMethod()}'", $request);
        }

        $headerLines = [];
        foreach ($request->getHeaders() as $headerName => $values) {
            $headerLines[] = $headerName.': '.$request->getHeaderLine($headerName);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerLines);

        return $curl;
    }
}

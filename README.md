# Diciotto

[![Latest Stable Version](https://poser.pugx.org/fain182/diciotto/v/stable)](https://packagist.org/packages/fain182/diciotto) [![Build Status](https://travis-ci.org/fain182/diciotto.svg?branch=master)](https://travis-ci.org/fain182/diciotto)

Diciotto is a simple PSR-18 compliant HTTP client library for PHP 7.

# Usage

## GET example

```php
    use Diciotto/HttpClient;
    use Diciotto/JsonPostRequest;

    $httpClient = new HttpClient();
    try {
      $response = $httpClient->sendRequest( new Request('http://') );
    } catch (ClientExceptionInterface $e) {

    }

    ?>
```

## POST TO JSON API

```php
    use Diciotto/HttpClient;
    use Diciotto/JsonRequest;

    $httpClient = new HttpClient();
    $request = new JsonRequest('https://httpbin.org/put', 'POST', ['name' => 'value']);
    try {
      $response = $httpClient->sendRequest($request);
    } catch (ClientExceptionInterface $e) {

    }

    ?>
```

![Imgur](https://i.imgur.com/ezZqrxR.png)


[![Latest Stable Version](https://poser.pugx.org/fain182/diciotto/v/stable)](https://packagist.org/packages/fain182/diciotto) [![Build Status](https://travis-ci.org/fain182/diciotto.svg?branch=master)](https://travis-ci.org/fain182/diciotto) [![Coverage Status](https://coveralls.io/repos/github/fain182/diciotto/badge.svg?branch=master)](https://coveralls.io/github/fain182/diciotto?branch=master)

Diciotto is a no-nonsense PSR-18 compliant HTTP client library for PHP 7.

## Principles

  - Documentation should be unnecessary
  - Provide sensible defaults
  - Explicit is better than implicit
  - Prefer a good Developer eXperience over performance
  - No surprises

## Install
```
    composer require fain182/diciotto
```

## How to...

### make a GET request

```php
    $httpClient = new HttpClient();
    $response = $httpClient->sendRequest( new Request('http://www.google.com') );
```

### make a POST request with body in JSON

```php
    $httpClient = new HttpClient();
    $request = new JsonRequest('https://httpbin.org/put', 'POST', ['name' => 'value']);
    $response = $httpClient->sendRequest($request);
```

### make a request with a different timeout
The default timeout is 15 seconds.

```php
    $httpClient = (new HttpClient())->withTimeout(30);
    $response = $httpClient->sendRequest( new Request('http://www.google.com') );
```

### make a request to a server with self-signed or invalid SSL certificate

```php
    $httpClient = (new HttpClient())->withCheckSslCertificates(false);
    $response = $httpClient->sendRequest( new Request('http://www.google.com') );
```

### make a request with a cookie

```php
    $httpClient = new HttpClient();
    $request = (new Request('http://www.google.com'))->withAddedCookie('name', 'value');
    $response = $httpClient->sendRequest( $request );
```

## Error handling
Diciotto raise exception if the request is invalid (`RequestException`), or if there are network problems (`NetworkException`). 
Response with status code 4xx or 5xx are treated the same way as the others, so no exception or error is raised.

## About

### Requirements

- Diciotto works with PHP 7.1 or above.

### License

Diciotto is licensed under the MIT License - see the `LICENSE` file for details

### Acknowledgements

Diciotto is built on top of [nyholm/psr7](https://github.com/Nyholm/psr7) that provides PSR-7 and PSR-17 implementation.

# Diciotto

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

## POST

```php
    use Diciotto/HttpClient;
    use Diciotto/JsonPostRequest;

    $httpClient = new HttpClient();
    $request = new JsonPostRequest('http://', ['ciao'=>1])
    try {
      $response = $httpClient->sendRequest( $request );
    } catch (ClientExceptionInterface $e) {

    }

    ?>
```

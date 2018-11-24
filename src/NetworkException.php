<?php


namespace Diciotto;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class NetworkException extends \RuntimeException implements NetworkExceptionInterface
{
    private $request;

    public function __construct(string $message = "", RequestInterface $request)
    {
        parent::__construct($message, 0, null);
        $this->request = $request;
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
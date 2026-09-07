<?php

namespace Saggre\WordPress\Repository\Test\Unit\Stub;

use Sabre\HTTP\Client;
use Sabre\HTTP\Request;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\Response;
use Sabre\HTTP\ResponseInterface;

/**
 * A SabreHTTP client that records requests and replays canned responses.
 */
class HttpClientStub extends Client
{
    /** @var RequestInterface[] */
    public array $requests = [];

    /** @var ResponseInterface[] */
    protected array $responses;

    public function __construct(ResponseInterface ...$responses)
    {
        parent::__construct();

        $this->responses = $responses;
    }

    /**
     * Build a stub replaying a single response.
     *
     * @param int $status
     * @param string $body
     * @return self
     */
    public static function respondWith(int $status, string $body): self
    {
        return new self(new Response($status, [], $body));
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        return array_shift($this->responses);
    }

    /**
     * Get the URL of the request sent last.
     *
     * @return string
     */
    public function getLastUrl(): string
    {
        /** @var Request $request */
        $request = end($this->requests);

        return $request->getUrl();
    }
}

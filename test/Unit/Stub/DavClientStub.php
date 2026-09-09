<?php

namespace Saggre\WordPress\Repository\Test\Unit\Stub;

use Sabre\DAV\Client;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\Response;
use Sabre\HTTP\ResponseInterface;

/**
 * A SabreDAV client that records requests and replays canned responses.
 */
class DavClientStub extends Client
{
    /** @var RequestInterface[] */
    public array $requests = [];

    /** @var ResponseInterface[] */
    protected array $responses = [];

    public function __construct()
    {
        parent::__construct(['baseUri' => 'https://plugins.svn.wordpress.org']);
    }

    /**
     * Queue a response to replay, in the order the requests arrive.
     *
     * @param string $body
     * @param int $status
     * @return self
     */
    public function willRespondWith(string $body, int $status = 200): self
    {
        $this->responses[] = new Response($status, [], $body);

        return $this;
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        return array_shift($this->responses);
    }
}

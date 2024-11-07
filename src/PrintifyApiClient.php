<?php

namespace Garissman\Printify;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PrintifyApiClient
{

    public ?PendingRequest $client = null;
    public $response = null;
    public $last_request = null;
    public $status_code = null;
    public $paginate = false;

    public function __construct(string $token = null)
    {
        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'Accept' => 'application/json',
        ];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        $options = [
            'base_uri' => 'https://api.printify.com/v1/',
            'verify' => false,
        ];
        $this->client = Http::withOptions($options)
            ->withHeaders($headers);

    }

    public static function exchangeCodeForToken(string $app_id, string $code)
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens?app_id=' . $app_id . '&code=' . $code, 'POST');
    }

    /**
     * Does a HTTP request for a client and handles errors correctly.
     *
     * @param string $uri - The URI to hit
     * @param string $method - The HTTP method
     * @param array $payload
     * @return PromiseInterface|Response - The response
     * @throws ConnectionException
     * @throws RequestException
     */
    public function doRequest(string $uri, string $method = 'GET', array $payload = []): PromiseInterface|Response
    {
        if ($method === 'GET') {
            if (!isset($payload['paginate'])) {
                $payload['paginate'] = true;
                if (!isset($payload['page'])) {
                    $payload['page'] = 1;
                }
            }
        }
        return $this->client->$method($uri, $payload)->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public static function updateAccessToken(string $app_id, string $refresh_token): PromiseInterface|Response
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens/refresh?app_id=' . $app_id . '&refresh_token=' . $refresh_token, 'POST');
    }
}

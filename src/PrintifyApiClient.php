<?php

namespace Garissman\Printify;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

class PrintifyApiClient
{

    public $client = null;
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
            $headers['Authorization'] = 'Bearer '.$token;
        }

        $this->client = new Client([
            'base_uri' => 'https://api.printify.com/v1/',
            'headers' => $headers,
        ]);
    }

    /**
     * Does a HTTP request for a client and handles errors correctly.
     *
     * @param string $uri - The URI to hit
     * @param string $method - The HTTP method
     * @param array $json - Any body params
     * @return object - The response
     */
    public function doRequest(string $uri, string $method = 'GET', array $json = []): ?array
    {
        $options = $this->formatRequest($uri, $method, $json);
        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (RequestException | ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
            $message = optional($response)->message;

            if (property_exists($response, "error")) {
                $message = $response->error;
            }

            if (property_exists($response, "errors")) {
                $message = $response->errors->reason;
            }

            throw new Exception($message, $e->getCode());
        }
        $this->status_code = $response->getStatusCode();
        if ($this->status_code !== 200 && $this->status_code !== 201) {
            $error_msg = strtr('Guzzle request failed. URI: {uri} Method: {method} with response: {response}', [
                '{uri}' => $uri,
                '{method}' => $method,
                '{response}' => $response->getResponse()->getBody()->getContents()
            ]);
            throw new Exception($error_msg);
        }
        $this->last_request = $this->response;
        $this->response = json_decode($response->getBody()->getContents(), true);
        if ($this->paginate && isset($this->response['last_page']) && $this->response['last_page'] > 1) {
            //generate links
            $requests = [];
            for ($i = $this->response['current_page']; $i <= $this->response['last_page']; $i++) {
                $url_parsed = parse_url($uri);
                parse_str($url_parsed['query'], $query_parts);
                if (isset($query_parts['page'])) {
                    unset($query_parts['page']);
                }
                $query_parts['page'] = $i;
                $url = $url_parsed['path'].self::formatQuery($query_parts);
                $requests[] = new Request('GET', $url);
            }
            $responses = Pool::batch($this->client, $requests, [
                'concurrency' => 15,
            ]);
            $this->response = $this->response['data'];
            foreach ($responses as $response) {
                $this->response = array_merge($this->response, json_decode($response->getBody()->getContents(), true)['data']);
            }
            $this->response = ['data' => $this->response];
        }
        return $this->response;
    }

    public function formatRequest(string $uri, string $method, array $json = [])
    {
        $options = [];
        if ($method === 'GET' && !empty($json)) {
            $uri .= '?';
            foreach ($json as $key => $param) {
                $uri .= $key.'='.$param.'&';
            }
            $uri = substr($uri, 0, strlen($uri) - 1);
        } else if (!empty($json)) {
            $options['json'] = $json;
        }
        return $options;
    }

    public static function exchangeCodeForToken(string $app_id, string $code)
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens?app_id='.$app_id.'&code='.$code, 'POST');
    }

    public static function updateAccessToken(string $app_id, string $refresh_token)
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens/refresh?app_id='.$app_id.'&refresh_token='.$refresh_token, 'POST');
    }

    /**
     * Formats a array into a query string
     *
     * @param array $query_options
     * @return string
     */
    public static function formatQuery(array $query_options): string
    {
        $query = '?';
        foreach ($query_options as $key => $value) {
            if ($key === 'paginate') {
                continue;
            }
            $query .= $key.'='.$value.'&';
        }
        $query = substr($query, 0, strlen($query) - 1);
        return $query;
    }
}

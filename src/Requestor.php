<?php

namespace Paymentez;

use GuzzleHttp\{Client, RequestOptions};
use Paymentez\Exceptions\RequestException;
use Psr\Http\Message\ResponseInterface;


class Requestor
{
    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var \StdClass
     */
    protected $response;


    /**
     * Requestor constructor.
     * @param array $apiUri
     * @param bool $production
     * @param string $authToken
     * @throws RequestException
     */
    public function __construct(array $apiUris, bool $production)
    {
        $env = ($production) ? 'production' : 'staging';
        $this->client = new Client([
            // Set the base paymentez uri
            'base_uri' => $apiUris[$env],

            'timeout' => Settings::DEFAULT_SECONDS_TIMEOUT
        ]);
    }

    /**
     * @param string $resource
     * @param array $body
     * @param array $headers
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $resource,
                         array $body,
                         array $headers = [],
                         bool $hasVersion = true): ResponseInterface
    {
        return $this->make('POST',
            $resource,
            $body,
            $headers,
            [],
            $hasVersion);
    }

    /**
     * @param string $resource
     * @param array $query
     * @param array $headers
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $resource,
                        array $query,
                        array $headers = [],
                        bool $hasVersion = true): ResponseInterface
    {
        return $this->make('GET',
            $resource,
            [],
            $headers,
            $query,
            $hasVersion);
    }

    /**
     * Make a request with Guzzle
     * @param string $type
     * @param string $resource
     * @param array $body
     * @param array $headers
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function make(string $type,
                         string $resource,
                         array $body,
                         array $headers,
                         array $query = [],
                         bool $hasVersion = true): ResponseInterface
    {
        $mergedHeaders = self::mergeHeaders(array_merge($headers, [
            'Auth-Token' => Paymentez::auth() // Generate new token for each request
        ]));

        $resourcePath = [
            Settings::API_VERSION,
            $resource
        ];

        if (!$hasVersion) {
            array_shift($resourcePath);
        }

        return $this->client->request($type, implode('/', $resourcePath), [
            RequestOptions::HEADERS => $mergedHeaders,
            RequestOptions::JSON => $body,
            RequestOptions::QUERY => $query,
            // RequestOptions::DEBUG => true
        ]);
    }

    /**
     * @param array $schema
     * @param array $params
     * @return bool
     * @throws RequestException
     */
    public function validateRequestParams(array $schema, array $params): bool
    {
        $validation = Helpers::validateArray($schema, $params);
        $total = $validation['errors']['total'];

        if ($total > 0) {
            error_log("[Paymentez Requestor]: Errors on params validation.");
            error_log(print_r($validation, 1));

            throw new RequestException("Error on params validation see the logs for more information.");
        }

        return true;
    }

    /**
     * @param array $headers
     * @return array
     */
    public static function mergeHeaders(array $headers): array
    {
        return array_merge(Settings::DEFAULT_HEADERS, $headers);
    }
}

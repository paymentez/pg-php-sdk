<?php

namespace Paymentez;

use DateTime;
use Paymentez\Exceptions\RequestException;
use Paymentez\Resources\Resource;

class Paymentez
{
    /**
     * Paymentez application code
     * @var string
     */
    private static $code;

    /**
     * Paymentes application key
     * @var string
     */
    private static $apiKey;

    /**
     * Paymentez environment
     * @var string
     */
    private static $production = false;

    /**
     * Set a credentials and environment for Paymentez API
     * @return void
     * @throws \Exception
     */
    public static function init(string $code, string $apiKey, bool $production = false)
    {
        self::$code = $code;
        self::$apiKey = $apiKey;
        self::$production = $production;
    }

    /**
     * Generate string of authenticate
     * @return string
     * @throws \Exception
     */
    public static function auth(): string
    {
        if (empty(self::$code) || empty(self::$apiKey)) {
            throw new RequestException("Missing Paymentez API key or code, ensure that execute init method.");
        }

        $now = (string)(new DateTime)->getTimestamp();

        $uniqToken = implode('', [
            self::$apiKey,
            $now
        ]);

        $uniqTokenHash = hash('sha256', $uniqToken);

        return base64_encode(implode(';', [
            self::$code,
            $now,
            $uniqTokenHash
        ]));
    }

    /**
     * Make a new instance on resource requested
     * @param string $name
     * @param array $arguments
     * @return Resource New instance of paymentez api resource
     * @throws Exceptions\RequestException
     */
    public static function __callStatic(string $name, array $arguments): Resource
    {
        if (!key_exists($name, Settings::API_RESOURCES)) {
            throw new RequestException("Undefined resource {$name} to access.");
        }

        $resourceClass = Settings::API_RESOURCES[$name]['class'];
        $apiType = Settings::API_RESOURCES[$name]['api'];

        return new $resourceClass(new Requestor(Settings::BASE_URL[$apiType], self::$production, self::auth()));
    }
}
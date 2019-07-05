<?php

namespace Paymentez;

use Paymentez\Resources\{
    Cash,
    Card
};

class Settings
{
    const DEFAULT_SECONDS_TIMEOUT = 10;
    const CCAPI = 'ccapi';
    const NOCCAPI = 'noccapi';
    const API_VERSION = "v2";

    const BASE_URL = [
        self::CCAPI => [
            'production' => "https://ccapi.paymentez.com",
            'staging' => "https://ccapi-stg.paymentez.com"
        ],
        self::NOCCAPI => [
            'production' => "https://noccapi-prod.paymentez.com",
            'staging' => "https://noccapi-stg.paymentez.com"
        ]
    ];

    const API_RESOURCES = [
        'card' => [
            'class' => Card::class,
            'api' => self::CCAPI
        ],
        'cash' => [
            'class' => Cash::class,
            'api' => self::NOCCAPI
        ],
    ];

    const DEFAULT_HEADERS = [
        'Content-Type' => "application/json"
    ];
}
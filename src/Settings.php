<?php

namespace Paymentez;

use Paymentez\Resources\{Card, Cash, Charge};

class Settings
{
    const DEFAULT_SECONDS_TIMEOUT = 90;
    const CCAPI = 'ccapi';
    const NOCCAPI = 'noccapi';
    const API_VERSION = "v2";

    const BASE_URL = [
        self::CCAPI => [
            'production' => "https://ccapi.paymentez.com",
            'staging' => "https://ccapi-stg.paymentez.com"
        ],
        self::NOCCAPI => [
            'production' => "https://noccapi.paymentez.com",
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
        'charge' => [
            'class' => Charge::class,
            'api' => self::CCAPI
        ]
    ];

    const DEFAULT_HEADERS = [
        'Content-Type' => "application/json"
    ];
}
<?php

namespace Paymentez\Resources;

use GuzzleHttp\Exception\RequestException;
use Paymentez\Exceptions\{PaymentezErrorException, ResponseException};
use stdClass;


class Cash extends Resource
{
    const GENERATE_ORDER_ENDPOINT = 'order';

    const ENDPOINTS = [
        self::GENERATE_ORDER_ENDPOINT => 'order/'
    ];

    /**
     * @param array $carrier
     * @param array $user
     * @param array $order
     * @return stdClass
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Paymentez\Exceptions\RequestException
     */
    public function generateOrder(array $carrier,
                                  array $user,
                                  array $order): stdClass
    {
        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $carrier);

        $this->getRequestor()->validateRequestParams([
            'id' => 'numeric',
            'email' => 'string'
        ], $user);

        $this->getRequestor()->validateRequestParams([
            'dev_reference' => 'string',
            'amount' => 'numeric',
            'expiration_days' => 'numeric',
            'recurrent' => 'bool',
            'description' => 'string'
        ], $order);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::GENERATE_ORDER_ENDPOINT],
                [
                    'carrier' => $carrier,
                    'user' => $user,
                    'order' => $order
                ], [],
                false);
        } catch (RequestException $clientException) {
            ResponseException::launch($clientException);
        }

        if ($response->getStatusCode() == 200) {
            $this->setData(json_decode($response->getBody()));
            return $this->getData();
        }

        throw new PaymentezErrorException("Can't generate cash order.");
    }
}
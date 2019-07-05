<?php

namespace Paymentez\Resources;

use Paymentez\Exceptions\{
    PaymentezErrorException,
    ResponseException
};

use GuzzleHttp\Exception\RequestException;


class Card extends Resource
{
    const ADD_ENDPOINT = 'add';
    const LIST_ENDPOINT = 'list';

    const ENDPOINTS = [
        self::ADD_ENDPOINT => "card/add",
        self::LIST_ENDPOINT => "card/list"
    ];

    /**
     * @param array $user
     * @param array $card
     * @return $this
     * @throws Exceptions\RequestException
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Resource
     */
    public function add(array $user, array $card): self
    {
        $this->getRequestor()->validateRequestParams([
            'id' => 'numeric',
            'email' => 'string'
        ], $user);

        $this->getRequestor()->validateRequestParams([
            'number' => 'string',
            'holder_name' => 'string',
            'expiry_month' => 'numeric',
            'expiry_year' => 'numeric',
            'cvc' => 'string',
            'type' => 'string'
        ], $card);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::ADD_ENDPOINT], [
                'user' => $user,
                'card' => $card
            ]);
        } catch (RequestException $exception) {
            ResponseException::launch($exception);
        }

        if ($response->getStatusCode() == 200) {
            $this->setData(json_decode($response->getBody()));
            return $this;
        }

        throw new PaymentezErrorException("Error on add card.");
    }

    /**
     * @param $uid
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Resource
     */
    public function getList($uid): self
    {
        $params = ['uid' => (string) $uid];
        $this->getRequestor()->validateRequestParams([
            'uid' => 'numeric'
        ], $params);

        try {
            $response = $this->getRequestor()->get(self::ENDPOINTS[self::LIST_ENDPOINT], $params);
        } catch (RequestException $clientException) {
            ResponseException::launch($clientException);
        }

        if ($response->getStatusCode() == 200) {
            $this->setData(json_decode($response->getBody()));
            return $this;
        }

        throw new PaymentezErrorException("Error on get list of cards.");
    }
}

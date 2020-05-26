<?php

namespace Paymentez\Resources;

use GuzzleHttp\Exception\RequestException;
use Paymentez\Exceptions\{PaymentezErrorException, ResponseException};
use stdClass;


class Card extends Resource
{
    const LIST_ENDPOINT = 'list';
    const DELETE_ENDPOINT = 'delete';

    const ENDPOINTS = [
        self::LIST_ENDPOINT => "card/list",
        self::DELETE_ENDPOINT => "card/delete/"
    ];

    /**
     * @param string $token
     * @param array $user
     * @return stdClass
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Paymentez\Exceptions\RequestException
     */
    public function delete(string $token, array $user): stdClass
    {
        $card = [
            'token' => $token
        ];

        $this->getRequestor()->validateRequestParams([
            'token' => 'string'
        ], $card);

        $this->getRequestor()->validateRequestParams([
            'id' => 'numeric'
        ], $user);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::DELETE_ENDPOINT], [
                'card' => $card,
                'user' => $user
            ]);
        } catch (RequestException $exception) {
            ResponseException::launch($exception);
        }

        if ($response->getStatusCode() == 200) {
            $this->setData(json_decode($response->getBody()));
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on delete card.");
    }

    /**
     * @param $uid
     * @return stdClass
     * @throws PaymentezErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Paymentez\Exceptions\RequestException
     */
    public function getList($uid): stdClass
    {
        $params = ['uid' => (string)$uid];
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
            return $this->getData();
        }

        throw new PaymentezErrorException("Error on get list of cards.");
    }
}

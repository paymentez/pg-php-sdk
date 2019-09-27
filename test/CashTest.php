<?php

use Paymentez\Exceptions\{PaymentezErrorException, RequestException};
use Paymentez\Paymentez;
use PHPUnit\Framework\TestCase;

final class CashTest extends TestCase
{
    protected $service;

    public function setUp()
    {
        Paymentez::init("MAGENTO_MX_CLIENT", "Obp8Dyu23X5Ssrillw2OGPH7ipi98b");
        $this->service = Paymentez::cash();
    }

    public function testSuccessGenerateOrder()
    {
        $order = $this->service->generateOrder([
            'id' => 'oxxo',
            'extra_params' => [
                'user' => [
                    'name' => "Juan",
                    'last_name' => "Perez"
                ]
            ]
        ], [
            'id' => "1",
            'email' => "randm@mail.com"
        ], [
            'dev_reference' => "XXXXXXXXXXXX",
            'amount' => 100,
            'expiration_days' => 1,
            'recurrent' => false,
            'description' => "XXXXXXXXXXXX"
        ]);

        $this->assertIsObject($order);
        $this->assertTrue(($order instanceof \stdClass));
        $this->assertObjectHasAttribute('application', $order);
        $this->assertObjectHasAttribute('commerce', $order);
        $this->assertObjectHasAttribute('user', $order);
        $this->assertObjectHasAttribute('transaction', $order);
    }

    public function testFailParamsGenerateOrder()
    {
        $this->expectException(RequestException::class);
        $order = $this->service->generateOrder([
            'random' => ""
        ], [], []);
    }

    public function testFailGenerateOrder()
    {
        $this->expectException(PaymentezErrorException::class);
        $this->service->generateOrder([
            'id' => 'oxxo',
            'extra_params' => []
        ], [
            'id' => "1",
            'email' => "randm@mail.com"
        ], [
            'dev_reference' => "XXXXXXXXXXXX",
            'amount' => 100,
            'expiration_days' => 1,
            'recurrent' => false,
            'description' => "XXXXXXXXXXXX"
        ]);
    }
}
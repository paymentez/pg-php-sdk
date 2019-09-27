<?php

use Paymentez\Exceptions\{PaymentezErrorException, RequestException};
use Paymentez\Paymentez;
use PHPUnit\Framework\TestCase;


final class ChargeTest extends TestCase
{
    protected $service;
    protected $successToken;

    public function setUp()
    {
        Paymentez::init("MAGENTO_MX_SERVER", "DKzCAv6EXXgQrC0hATOltXZ6OZ7Zss");

        $this->service = Paymentez::charge();
        $this->demoToken = "13086227242634397294";
    }

    public function testSuccessCreate()
    {
        $amount = 99.10;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $newCharge = $this->service->create($this->demoToken, $orderDetails, $userDetails);

        $this->assertIsObject($newCharge);
        $this->assertTrue(($newCharge instanceof \stdClass));
        $this->assertObjectHasAttribute('transaction', $newCharge);
        $this->assertObjectHasAttribute('card', $newCharge);
        $this->assertEquals("success", $newCharge->transaction->status);
        $this->assertEquals($amount, $newCharge->transaction->amount);
    }

    public function testFailCreate()
    {
        $this->expectException(PaymentezErrorException::class);

        $amount = 99999999999999999999999999999999999999999999;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $this->service->create($this->demoToken, $orderDetails, $userDetails);
    }

    public function testSuccessAuthorize()
    {
        $amount = 99.10;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);

        $this->assertIsObject($auth);
        $this->assertTrue(($auth instanceof \stdClass));
        $this->assertObjectHasAttribute('transaction', $auth);
        $this->assertObjectHasAttribute('card', $auth);
        $this->assertEquals("success", $auth->transaction->status);
        $this->assertEquals($amount, $auth->transaction->amount);
    }

    public function testFailParamsAuthorize()
    {
        $this->expectException(RequestException::class);

        $this->service->authorize($this->demoToken, [
            'dev_reference' => 12,
            'foo' => 'XXXXXXXX'
        ], []);
    }

    public function testFailAuthorize()
    {
        $this->expectException(PaymentezErrorException::class);

        $amount = 100.00;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $this->service->authorize("random_super_card_token", $orderDetails, $userDetails);
    }

    public function testSuccessCapture()
    {
        $amount = 99.10;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);

        $this->assertObjectHasAttribute('transaction', $auth);

        $capture = $this->service->capture($auth->transaction->id);

        $this->assertIsObject($capture);
        $this->assertTrue(($capture instanceof \stdClass));
        $this->assertObjectHasAttribute('transaction', $capture);
        $this->assertObjectHasAttribute('card', $capture);
        $this->assertEquals("success", $capture->transaction->status);
    }

    public function testSuccessCaptureUpdateAmount()
    {
        $amount = 99.10;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);

        $this->assertObjectHasAttribute('transaction', $auth);

        $amount = 10.45;
        $capture = $this->service->capture($auth->transaction->id, $amount);

        $this->assertIsObject($capture);
        $this->assertTrue(($capture instanceof \stdClass));
        $this->assertObjectHasAttribute('transaction', $capture);
        $this->assertObjectHasAttribute('card', $capture);
        $this->assertEquals("success", $capture->transaction->status);
        $this->assertEquals($amount, $capture->transaction->amount);
    }

    public function testFailCapture()
    {
        $this->expectException(PaymentezErrorException::class);
        $this->service->capture("RANDOM-ID-TRANSACTION");
    }

    /* TODO
    public function testSuccessVerify()
    {
        $amount = 99.0;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);
        $this->assertObjectHasAttribute('transaction', $auth);
        $capture = $this->service->capture($auth->transaction->id);
        $this->assertObjectHasAttribute('transaction', $capture);

        unset($userDetails['email']);

        $verify = $this->service->verify("BY_AUTH_CODE",
            $auth->transaction->authorization_code,
            $auth->transaction->id,
            $userDetails);

        $this->assertIsObject($verify);
        $this->assertTrue(($verify instanceof \stdClass));
        $this->assertObjectHasAttribute('transaction', $verify);
        $this->assertObjectHasAttribute('card', $verify);
        $this->assertEquals($amount, $verify->transaction->amount);
    }*/

    public function testSuccessCancel()
    {
        $amount = 99.0;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);
        $this->assertObjectHasAttribute('transaction', $auth);

        $refund = $this->service->refund($auth->transaction->id);

        $this->assertIsObject($refund);
        $this->assertTrue(($refund instanceof \stdClass));
        $this->assertObjectHasAttribute('status', $refund);
        $this->assertObjectHasAttribute('detail', $refund);
        $this->assertEquals('success', $refund->status);
    }

    public function testFailCancel()
    {
        $this->expectException(PaymentezErrorException::class);
        $this->service->refund("RANDOM-ID-TRANSACTION");
    }

    public function testSuccessCancelDifferentAmount()
    {
        $amount = 99.0;
        $orderDetails = [
            'dev_reference' => "XXXXXXXXXX",
            'amount' => $amount,
            'description' => "XXXXXXXXXX",
            'vat' => 0.00
        ];

        $userDetails = [
            'id' => "4",
            'email' => "cbenavides@paymentez.com"
        ];

        $auth = $this->service->authorize($this->demoToken, $orderDetails, $userDetails);
        $this->assertObjectHasAttribute('transaction', $auth);

        $amount = 80.50;
        $refund = $this->service->refund($auth->transaction->id, $amount);

        $this->assertIsObject($refund);
        $this->assertTrue(($refund instanceof \stdClass));
        $this->assertObjectHasAttribute('status', $refund);
        $this->assertObjectHasAttribute('detail', $refund);
        $this->assertEquals('success', $refund->status);
        $this->assertStringContainsString('partial', $refund->detail);
        $this->assertStringContainsString('refunded', $refund->detail);
    }
}
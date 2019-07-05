<?php

use PHPUnit\Framework\TestCase;
use Paymentez\Paymentez;
use Paymentez\Exceptions\{
    PaymentezErrorException,
    RequestException
};

final class PaymentezTest extends TestCase
{
    public function testInvalidResource()
    {
        $this->expectException(RequestException::class);

        Paymentez::init("random", "random");
        Paymentez::randomResource();
    }

}
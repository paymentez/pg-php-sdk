<?php

use Paymentez\Exceptions\{RequestException};
use Paymentez\Paymentez;
use PHPUnit\Framework\TestCase;

final class PaymentezTest extends TestCase
{
    public function testInvalidResource()
    {
        $this->expectException(RequestException::class);

        Paymentez::init("random", "random");
        Paymentez::randomResource();
    }

}
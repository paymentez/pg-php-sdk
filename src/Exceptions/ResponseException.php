<?php

namespace Paymentez\Exceptions;

use GuzzleHttp\Exception\RequestException;


class ResponseException
{
    /**
     * @param ClientException $clientException
     * @param bool $logger
     * @return void
     * @throws PaymentezErrorException
     */
    public static function launch(RequestException $clientException, bool $logger = true)
    {
        $error = $clientException->getResponse();
        $rawResponse = $error->getBody()->getContents();
        $errorPayload = json_decode($rawResponse);
        $help = $errorPayload->error->help;
        $desc = $errorPayload->error->description;
        $type = $errorPayload->error->type;
        $exceptionText = !empty($help) ? $help : $desc;
        $responseHttpCode = $clientException->getCode();

        if ($logger) {
            error_log("=========== ERROR ON PAYMENTEZ API ===========");
            error_log("Type of error: {$type}");
            error_log("Description: {$desc}");
            error_log("Some of help: {$help}");
            error_log("HTTP code: {$responseHttpCode}");
            error_log("Raw API response: {$rawResponse}");
            error_log("=========== // ERROR ON PAYMENTEZ API // ===========");
        }

        throw new PaymentezErrorException("[{$type}]: {$exceptionText}", $responseHttpCode);
    }
}
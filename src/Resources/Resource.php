<?php

namespace Paymentez\Resources;

use Paymentez\Exceptions\ResourceException;
use Paymentez\Requestor;

abstract class Resource
{
    /**
     * @var object
     */
    private $data;

    /**
     * @var Requestor
     */
    private $requestor;

    /**
     * Resource constructor.
     * @param Requestor $requestor
     */
    public function __construct(Requestor $requestor)
    {
        $this->requestor = $requestor;
    }

    /**
     * @param $name
     * @return mixed
     * @throws ResourceException
     */
    public function __get($name)
    {
        if (!property_exists($this->data, $name)) {
            throw new ResourceException("Undefined property with name {$name}.");
        }

        return $this->data->$name;
    }

    /**
     * @param \stdClass $data
     * @return Resource
     */
    protected function setData(\stdClass $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getData(): \stdClass
    {
        return $this->data;
    }

    /**
     * @return Requestor
     */
    public function getRequestor(): Requestor
    {
        return $this->requestor;
    }
}
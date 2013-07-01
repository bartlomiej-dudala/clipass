<?php

namespace CliPass\Stubs;

use Buzz\Message\Response AS BuzzResponse;
use CliPass\Crypt;
use CliPass\Identity;
use CliPass\Response\BuilderInterface;
use CliPass\StringEncoder\Base64Encoder;

class StubResponseBuilder implements BuilderInterface
{
    private $response;

    public function __construct()
    {
        $this->response = new StubValidResponse();
    }

    /**
     * @param BuzzResponse $buzzResponse
     * @param Identity $identity
     * @param Crypt $crypt
     * @param Base64Encoder $base64Encoder
     * @return \CliPass\Response\Response|StubValidResponse
     */
    public function build(BuzzResponse $buzzResponse, Identity $identity, Crypt $crypt, Base64Encoder $base64Encoder)
    {
        return $this->response;
    }

    /**
     * @param \CliPass\Stubs\StubValidResponse $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \CliPass\Stubs\StubValidResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
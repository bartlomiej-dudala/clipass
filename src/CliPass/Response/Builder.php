<?php

namespace CliPass\Response;

use Buzz\Message\Response AS BuzzResponse;
use CliPass\Crypt;
use CliPass\Identity;
use CliPass\StringEncoder\Base64Encoder;

class Builder implements BuilderInterface
{
    /**
     * @param BuzzResponse $buzzResponse
     * @param Identity $identity
     * @param Crypt $crypt
     * @param Base64Encoder $base64Encoder
     * @return Response
     */
    public function build(BuzzResponse $buzzResponse, Identity $identity, Crypt $crypt, Base64Encoder $base64Encoder)
    {
        return new Response($buzzResponse, $identity, $crypt, $base64Encoder);
    }
}

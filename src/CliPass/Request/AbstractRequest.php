<?php

namespace CliPass\Request;


use CliPass\Crypt;
use CliPass\Identity;
use CliPass\StringEncoder\Base64Encoder;

abstract class AbstractRequest
{
    /** @var Crypt */
    protected $crypt;

    /** @var  Identity */
    protected $identity;

    /** @var Base64Encoder */
    protected $base64Encoder;

    /**
     * @param Crypt $crypt
     * @param Identity $identity
     * @param Base64Encoder $base64Encoder
     */
    public function __construct(Crypt $crypt, Identity $identity, Base64Encoder $base64Encoder)
    {
        $this->crypt = $crypt;
        $this->identity = $identity;
        $this->base64Encoder = $base64Encoder;
    }

    /**
     * @return string
     */
    abstract public function getJSONEncodedParams();
}
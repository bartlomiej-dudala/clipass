<?php

namespace CliPass;

use Buzz\Browser;
use CliPass\Request\Associate;
use CliPass\Request\TestAssociate;
use CliPass\Response\BuilderInterface;
use CliPass\StringEncoder\Base64Encoder;

class Associator
{
    /** @var \Buzz\Browser */
    protected $buzz;

    /** @var Identity */
    protected $identity;

    /** @var Crypt */
    protected $crypt;

    /** @var  Base64Encoder */
    protected $base64Encoder;

    /** @var ResponseBuilderInterface */
    protected $responseBuilder;

    /**
     * @param \Buzz\Browser $browser
     */
    public function __construct(
            Identity $identity,
            Crypt $crypt,
            Base64Encoder $base64Encoder,
            Browser $browser,
            BuilderInterface $responseBuilder
    )
    {
        $this->buzz = $browser;
        $this->crypt = $crypt;
        $this->identity = $identity;
        $this->base64Encoder = $base64Encoder;
        $this->responseBuilder = $responseBuilder;
    }

    public function associate()
    {
        if(!$this->identity->isEmpty()) {
            if($this->testAssociate()) {
                return true;
            }
        }

        $request = new Associate($this->crypt, $this->identity, $this->base64Encoder);

        $buzzResponse = $this->buzz->post('http://localhost:19455', array(), $request->getJSONEncodedParams());

        $response = $this->responseBuilder->build($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        if($response->validate()) {
            $this->identity->setKeyName($response->getId());

            $this->identity->saveInStorage();
            return;
        }

        throw new CliPassException('Associate request - failed');
    }

    private function testAssociate()
    {
        $request = new TestAssociate($this->crypt, $this->identity, $this->base64Encoder);

        $buzzResponse = $this->buzz->post('http://localhost:19455', array(), $request->getJSONEncodedParams());
        $response = $this->responseBuilder->build($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        if($response->validate()) {
            return true;
        }

    }
}

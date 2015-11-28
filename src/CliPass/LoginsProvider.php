<?php

namespace CliPass;

use Buzz\Browser;
use CliPass\Request\Associate;
use CliPass\Request\GetLogins;
use CliPass\Request\TestAssociate;
use CliPass\Response\BuilderInterface;
use CliPass\Response\ResponseInterface;
use CliPass\StringEncoder\Base64Encoder;

class LoginsProvider
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

    public function getLogins($name)
    {
        // TODO: broken TDD - needs to write tests!

        if(empty($name)) {
            throw new CliPassException('Nothing to search');
        }

        $request = new GetLogins($this->crypt, $this->identity, $this->base64Encoder);
        $request->setSearchKey($name);

        $buzzResponse = $this->buzz->post('http://localhost:19455', array('Content-Type: application/json'), $request->getJSONEncodedParams($name));

        /** @var ResponseInterface $response */
        $response = $this->responseBuilder->build($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        if(!$response->validate()) {
            return array();
        }

        return $response->getEntries();

    }
}

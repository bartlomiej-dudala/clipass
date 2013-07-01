<?php

namespace CliPass\Test;


use CliPass\Associator;
use CliPass\CliPassException;
use CliPass\Exception;
use CliPass\ResponseBuilderInterface;
use CliPass\Stubs\StubInvalidResponse;
use CliPass\Stubs\StubResponseBuilder;
use Phake;

class AssociatorTest extends \PHPUnit_Framework_TestCase
{
    private $buzz;

    private $buzzResponse;

    private $crypt;

    private $identity;

    private $base64Encoder;

    /** @var Associator */
    private $associator;

    /** @var  StubResponseBuilder */
    private $responseBuilder;


    public function setUp()
    {
        $this->buzz = Phake::mock('Buzz\Browser');
        $this->buzzResponse = Phake::mock('\Buzz\Message\Response');
        $this->crypt = Phake::mock('\CliPass\Crypt');
        $this->identity = Phake::mock('\CliPass\Identity');
        $this->base64Encoder = Phake::mock('\CliPass\StringEncoder\Base64Encoder');
        $this->responseBuilder = new StubResponseBuilder();

        $this->associator = new Associator(
            $this->identity,
            $this->crypt,
            $this->base64Encoder,
            $this->buzz,
            $this->responseBuilder
        );
    }

    /**
     * @test
     */
    public function should_make_Associate_request_when_identity_is_empty()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(true);
        Phake::when($this->buzz)->post(Phake::anyParameters())->thenReturn($this->buzzResponse);
        $this->associator->associate();
        $this->assertRequest($this->stringContains('"RequestType":"associate"'), 'http://localhost:19455');
    }

    /**
     * @test
     */
    public function associate_should_save_identity_if_response_is_valid()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(true);
        $this->mockBuzzResponseWithSuccessfullFlagAndContent(true);
        $this->responseBuilder->getResponse()->setId('response-id');

        $this->associator->associate();

        Phake::verify($this->identity, Phake::times(1))->setKeyName('response-id');
        Phake::verify($this->identity, Phake::times(1))->saveInStorage();
    }

    /**
     * @param $urlMatcher
     * @param $headersMatcher
     * @param $paramsMatcher
     */
    private function assertRequest($paramsMatcher = null, $urlMatcher = null, $headersMatcher = null )
    {
        Phake::verify($this->buzz, Phake::times(1))->post(
            $this->getMatcherAnythingIfNull($urlMatcher),
            $this->getMatcherAnythingIfNull($headersMatcher),
            $this->getMatcherAnythingIfNull($paramsMatcher)
        );
    }

    private function getMatcherAnythingIfNull($matcher)
    {
        if($matcher !== null) {
            return $matcher;
        }

        return $this->anything();
    }

    private function mockBuzzResponseWithSuccessfullFlagAndContent($success, $content = null)
    {

        Phake::when($this->buzzResponse)->isSuccessful()->thenReturn($success);

        if(!is_null($content)) {
            Phake::when($this->buzzResponse)->getContent()->thenReturn(json_encode($content));
        }
        Phake::when($this->buzz)->post(\Phake::anyParameters())->thenReturn($this->buzzResponse);
    }

    /**
     * @test
     */
    public function should_make_TestAssociate_request_when_identity_is_not_empty()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(false);

        Phake::when($this->buzz)->post(Phake::anyParameters())->thenReturn($this->buzzResponse);

        $this->associator->associate();
        $this->assertRequest($this->stringContains('"RequestType":"test-associate"', 'http://localhost:19455'));
    }

    /**
     * @test
     */
    public function should_not_make_Associate_request_when_TestAssociate_succeed()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(false);

        Phake::when($this->buzz)->post(Phake::anyParameters())->thenReturn($this->buzzResponse);

        $this->assertTrue($this->associator->associate());

        Phake::verify($this->buzz, Phake::never())->post(
            $this->anything(),
            $this->anything(),
            $this->stringContains('"RequestType":"associate"')
        );
    }

    /**
     * @test
     * @expectedException CliPass\CliPassException
     */
    public function should_make_Associate_request_when_TestAssociate_fails()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(false);

        Phake::when($this->buzz)->post(Phake::anyParameters())->thenReturn($this->buzzResponse);

        $this->responseBuilder->setResponse(new StubInvalidResponse());

        $this->associator->associate();

        $this->assertRequest($this->stringContains('"RequestType":"test-associate"', 'http://localhost:19455'));
        $this->assertRequest($this->stringContains('"RequestType":"associate"', 'http://localhost:19455'));
    }

    /**
     * @test
     * @expectedException CliPass\CliPassException
     */
    public function should_throw_exception_when_assosiate_fails()
    {
        Phake::when($this->identity)->isEmpty()->thenReturn(true);

        $this->mockBuzzResponseWithSuccessfullFlagAndContent(true);

        $this->responseBuilder->setResponse(new StubInvalidResponse());
        $this->associator->associate();


    }

}

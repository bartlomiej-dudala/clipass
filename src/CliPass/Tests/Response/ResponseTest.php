<?php

namespace CliPass\Tests\Response;


use CliPass\Response\Entry;
use CliPass\Response\Response;
use Phake;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    private $crypt;

    private $identity;

    private $base64Encoder;

    public function setUp()
    {
        $this->crypt = Phake::mock('\CliPass\Crypt');
        $this->identity = Phake::mock('\CliPass\Identity');
        $this->base64Encoder = Phake::mock('\CliPass\StringEncoder\Base64Encoder');
    }

    /**
     * @test
     */
    public function validate_should_return_false_when_buzz_connection_fails()
    {
        $buzzResponse = $this->mockBuzzResponseWithSuccessfullFlagAndContent(false);
        $response = new Response($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);
        $this->assertFalse($response->validate());
    }

    /** @test */
    public function validate_should_return_false_when_nonce_is_empty()
    {
        $responseData = array();
        $buzzResponse = $this->mockBuzzResponseWithSuccessfullFlagAndContent(true, $responseData);
        $response = new Response($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        $this->assertFalse($response->validate());

    }

    /**
     * @test
     */
    public function validate_should_return_true_if_encrypted_verifier_is_equals_to_nonce()
    {
        $responseData = array(
            'Nonce' => 'base64encodedNonce',
            'Verifier' => 'base64encodedVerifier',
        );

        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->base64Encoder)->decode('base64encodedNonce')->thenReturn('nonce');
        Phake::when($this->base64Encoder)->decode('base64encodedVerifier')->thenReturn('verifier');

        Phake::when($this->base64Encoder)->decode('base64encodedDecryptedNonce')->thenReturn('nonce');

        $buzzResponse = $this->mockBuzzResponseWithSuccessfullFlagAndContent(true, $responseData);

        Phake::when($this->crypt)->decrypt('verifier', 'key', 'nonce')
            ->thenReturn('base64encodedDecryptedNonce');

        $response = new Response($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        $this->assertTrue($response->validate());
    }

    /**
     * @test
     */
    public function validate_should_return_id_from_response()
    {
        $responseData = array(
            'Id' => '123',
        );

        $buzzResponse = $this->mockBuzzResponseWithSuccessfullFlagAndContent(true, $responseData);
        $response = new Response($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        $this->assertEquals('123', $response->getId());
    }

    private function mockBuzzResponseWithSuccessfullFlagAndContent($success, $content = null)
    {
        $buzzResponse = Phake::mock('\Buzz\Message\Response');
        Phake::when($buzzResponse)->isSuccessful()->thenReturn($success);

        if(!is_null($content)) {
            Phake::when($buzzResponse)->getContent()->thenReturn(json_encode($content));
        }
        return $buzzResponse;
    }

    /**
     * @test
     */
    public function should_return_entries()
    {
        $responseData = array(
            'Nonce' => 'base64encodedNonce',
            'Entries' => array(
                array(
                    'Login' => 'base64encodedCryptedLogin',
                    'Password' => 'base64encodedCryptedPassword',
                    'Uuid' => 'base64encodedCryptedUuid',
                    'Name' => 'base64encodedCryptedName',
                )
            )
        );

        $buzzResponse = $this->mockBuzzResponseWithSuccessfullFlagAndContent(true, $responseData);
        $response = new Response($buzzResponse, $this->identity, $this->crypt, $this->base64Encoder);

        $entry = new Entry($this->identity, $this->crypt);
        $entry->setCryptedLogin('cryptedLogin');
        $entry->setCryptedPassword('cryptedPassword');
        $entry->setCryptedUuid('cryptedUuid');
        $entry->setCryptedName('cryptedName');
        $entry->setIv('keyName');

        Phake::when($this->identity)->getKey()->thenReturn('key');

        Phake::when($this->base64Encoder)->decode('base64encodedCryptedLogin')->thenReturn('cryptedLogin');
        Phake::when($this->base64Encoder)->decode('base64encodedCryptedPassword')->thenReturn('cryptedPassword');
        Phake::when($this->base64Encoder)->decode('base64encodedCryptedUuid')->thenReturn('cryptedUuid');
        Phake::when($this->base64Encoder)->decode('base64encodedCryptedName')->thenReturn('cryptedName');
        Phake::when($this->base64Encoder)->decode('base64encodedNonce')->thenReturn('nonce');

        Phake::when($this->crypt)->decrypt('cryptedLogin', 'key', 'nonce')->thenReturn('login');
        Phake::when($this->crypt)->decrypt('cryptedPassword', 'key', 'nonce')->thenReturn('password');
        Phake::when($this->crypt)->decrypt('cryptedUuid', 'key', 'nonce')->thenReturn('uuid');
        Phake::when($this->crypt)->decrypt('cryptedName', 'key', 'nonce')->thenReturn('name');

        /** @var Entry $resultEntry */
        $resultEntry = current($response->getEntries());

        $this->assertEquals($entry->getName(), $resultEntry->getName());
        $this->assertEquals($entry->getLogin(), $resultEntry->getLogin());
        $this->assertEquals($entry->getPassword(), $resultEntry->getPassword());
        $this->assertEquals($entry->getUuid(), $resultEntry->getUuid());
    }
}

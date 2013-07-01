<?php

namespace CliPass\Tests\Request;


use CliPass\Request\TestAssociate;
use Phake;

class TestAssociateTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    private $crypt;

    private $identity;

    private $base64Encoder;

    public function setUp()
    {
        $this->crypt = Phake::mock('\CliPass\Crypt');
        $this->identity = Phake::mock('\CliPass\Identity');
        $this->base64Encoder = Phake::mock('\CliPass\StringEncoder\Base64Encoder');
        $this->request = new TestAssociate($this->crypt, $this->identity, $this->base64Encoder);
    }

    /**
     * @test
     */
    public function should_have_correctly_RequestType()
    {
        $this->assertContains('"RequestType":"test-associate"', $this->request->getJSONEncodedParams(), '', false);
    }

    /**
     * @test
     */
    public function should_have_key_from_identity()
    {
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->base64Encoder)->encode('key')->thenReturn('encodedKey');

        $this->assertContains('"Key":"encodedKey"', $this->request->getJSONEncodedParams(), '', false);
    }

    /**
     * @test
     */
    public function should_have_generated_IV()
    {
        Phake::when($this->crypt)->generateIv()->thenReturn('nonce');
        Phake::when($this->base64Encoder)->encode('nonce')->thenReturn('encodedNonce');

        $this->assertContains('"Nonce":"encodedNonce"', $this->request->getJSONEncodedParams(), '', false);
    }

    /**
     * @test
     */
    public function should_contains_verifier_as_encrypted_base64encoded_iv()
    {
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->generateIv()->thenReturn('iv');
        Phake::when($this->crypt)->encrypt(Phake::anyParameters())->thenReturn('verifier');

        Phake::when($this->base64Encoder)->encode('verifier')->thenReturn('encodedVerifier');
        Phake::when($this->base64Encoder)->encode('iv')->thenReturn('encodedIv');

        $this->assertContains('"Verifier":"encodedVerifier"', $this->request->getJSONEncodedParams(), '', false);
        Phake::verify($this->crypt, Phake::times(1))->encrypt('encodedIv', 'key', 'iv');
    }

    /**
     * @test
     */
    public function should_have_Id_as_keyName_from_identity()
    {
        Phake::when($this->identity)->getKeyName()->thenReturn('keyName');
        $this->assertContains('"Id":"keyName"', $this->request->getJSONEncodedParams(), '', false);
    }
}

<?php

namespace CliPass\Tests\Request;

use CliPass\Request\GetLogins;
use Phake;

class GetLoginsTest extends \PHPUnit_Framework_TestCase
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
        $this->request = new GetLogins($this->crypt, $this->identity, $this->base64Encoder);
        $this->request->setSearchKey('name');
    }

    /**
     * @test
     */
    public function should_have_correctly_RequestType()
    {
        $this->assertContains('"RequestType":"get-logins"', $this->request->getJSONEncodedParams(), '', false);
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

        Phake::when($this->base64Encoder)->encode('iv')->thenReturn('encodedIv');
        Phake::when($this->crypt)->encrypt('encodedIv', 'key', 'iv')->thenReturn('verifier');
        Phake::when($this->base64Encoder)->encode('verifier')->thenReturn('encodedVerifier');

        $this->assertContains('"Verifier":"encodedVerifier"', $this->request->getJSONEncodedParams(), '', false);
    }

    /**
     * @test
     */
    public function should_have_Id_as_keyName_from_identity()
    {
        Phake::when($this->identity)->getKeyName()->thenReturn('keyName');
        $this->assertContains('"Id":"keyName"', $this->request->getJSONEncodedParams(), '', false);
    }

    /**
     * @test
     */
    public function should_contains_crypted_Url_provider_by_client()
    {
        $this->request->setSearchKey('url');
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->generateIv()->thenReturn('iv');

        Phake::when($this->crypt)->encrypt('url', 'key', 'iv')->thenReturn('encryptedUrl');
        Phake::when($this->base64Encoder)->encode('encryptedUrl')->thenReturn('encodedUrl');

        $this->assertContains('"Url":"encodedUrl"', $this->request->getJSONEncodedParams(), '', false);
        $this->assertContains('"SubmitUrl":"encodedUrl"', $this->request->getJSONEncodedParams(), '', false);
    }

    // TODO: [minor] - handle problems with connection, and empty identity


}

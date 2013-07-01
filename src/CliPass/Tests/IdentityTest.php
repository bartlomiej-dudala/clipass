<?php

namespace CliPass\Test;

use CliPass\Identity;
use Gaufrette\Adapter\InMemory;
use Phake;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /** @var InMemory */
    private $storageAdapter;

    private $base64Encoder;

    public function setUp()
    {
        $this->storageAdapter = new InMemory(array('file' => ''));
        $this->base64Encoder = Phake::mock('\CliPass\StringEncoder\Base64Encoder');
    }

    /**
     * @test
     */
    public function should_save_identity_in_storage()
    {
        $this->getFilledIdentity()->saveInStorage();
        $this->assertTrue($this->storageAdapter->exists('.clipass.identity'));
    }

    /**
     * @return \CliPass\Identity
     */
    private function getFilledIdentity()
    {
        $identity = new Identity($this->storageAdapter, $this->base64Encoder);

        $identity->setKey('mySecretKey');
        $identity->setKeyName('NameOfTheKey');

        return $identity;
    }

    /**
     * @test
     */
    public function should_correctly_read_identity_from_storage()
    {
        Phake::when($this->base64Encoder)->encode('mySecretKey')->thenReturn('encodedMySecretKey');
        Phake::when($this->base64Encoder)->encode('NameOfTheKey')->thenReturn('encodedNameOfTheKey');

        Phake::when($this->base64Encoder)->decode('encodedMySecretKey')->thenReturn('decodedMySecretKey');
        Phake::when($this->base64Encoder)->decode('encodedNameOfTheKey')->thenReturn('decodedNameOfTheKey');

        $this->getFilledIdentity()->saveInStorage();

        $clearIdentity = new Identity($this->storageAdapter, $this->base64Encoder);
        $this->assertEquals('decodedMySecretKey', $clearIdentity->getKey());
        $this->assertEquals('decodedNameOfTheKey', $clearIdentity->getKeyName());

    }

    /**
     * @test
     */
    public function should_be_marked_as_empty_when_key_and_keyName_are_null()
    {
        $identity = new Identity($this->storageAdapter, $this->base64Encoder);
        $this->assertTrue($identity->isEmpty());
    }
}

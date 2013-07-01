<?php

namespace CliPass\Test;

use CliPass\Identity;
use Gaufrette\Adapter\InMemory;

class
IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemory
     */
    protected $storageAdapter;

    public function setUp()
    {
        $this->storageAdapter = new InMemory(array('file' => ''));
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
        $identity = new Identity($this->storageAdapter);

        $identity->setKey('mySecretKey');
        $identity->setKeyName('NameOfTheKey');

        return $identity;
    }

    /**
     * @test
     */
    public function should_correctly_read_identity_from_storage()
    {
        $this->getFilledIdentity()->saveInStorage();

        $clearIdentity = new Identity($this->storageAdapter);
        $this->assertEquals('mySecretKey', $clearIdentity->getKey());
        $this->assertEquals('NameOfTheKey', $clearIdentity->getKeyName());

    }

    /**
     * @test
     */
    public function should_be_marked_as_empty_when_key_and_keyName_are_null()
    {
        $identity = new Identity($this->storageAdapter);
        $this->assertTrue($identity->isEmpty());
    }
}

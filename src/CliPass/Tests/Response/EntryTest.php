<?php

namespace CliPass\Tests\Response;

use CliPass\Response\Entry;
use Phake;

class EntryTest extends \PHPUnit_Framework_TestCase
{
    private $identity;
    private $crypt;

    /** @var  Entry */
    private $entry;

    public function setUp()
    {
        $this->identity = Phake::mock('\CliPass\Identity');
        $this->crypt = Phake::mock('\CliPass\Crypt');

        $this->entry = new Entry($this->identity, $this->crypt);
    }

    /** @test */
    public function should_return_decrypted_name()
    {
        $this->entry->setCryptedName('crypted');
        $this->entry->setIv('IV');
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->decrypt('crypted', 'key', 'IV')->thenReturn('decrypted');

        $this->assertEquals('decrypted', $this->entry->getName());
    }

    /** @test */
    public function should_return_decrypted_login()
    {
        $this->entry->setCryptedLogin('crypted');
        $this->entry->setIv('IV');
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->decrypt('crypted', 'key', 'IV')->thenReturn('decrypted');

        $this->assertEquals('decrypted', $this->entry->getLogin());
    }

    /** @test */
    public function should_return_decrypted_uuid()
    {
        $this->entry->setCryptedUuid('crypted');
        $this->entry->setIv('IV');
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->decrypt('crypted', 'key', 'IV')->thenReturn('decrypted');

        $this->assertEquals('decrypted', $this->entry->getUuid());
    }

    /** @test */
    public function should_return_decrypted_password()
    {
        $this->entry->setCryptedPassword('crypted');
        $this->entry->setIv('IV');
        Phake::when($this->identity)->getKey()->thenReturn('key');
        Phake::when($this->crypt)->decrypt('crypted', 'key', 'IV')->thenReturn('decrypted');

        $this->assertEquals('decrypted', $this->entry->getPassword());
    }
}

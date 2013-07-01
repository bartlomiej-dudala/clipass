<?php

namespace CliPass\Test;

use CliPass\KeePassConnector;
use CliPass\ResponseBuilderInterface;
use Phake;

class KeePassConnectorTest extends \PHPUnit_Framework_TestCase
{
    private $associator;
    private $loginsProvider;

    /** @var KeePassConnector */
    private $connector;

    public function setUp()
    {
        $this->associator = Phake::mock('\CliPass\Associator');
        $this->loginsProvider = Phake::mock('\CliPass\LoginsProvider');

        $this->connector = new KeePassConnector($this->associator, $this->loginsProvider);
    }

    /** @test */
    public function should_ask_for_logins_when_associated()
    {
        Phake::when($this->associator)->associate()->thenReturn(true);

        Phake::when($this->loginsProvider)->getLogins('key')->thenReturn(array(1));

        $this->assertSame(array(1), $this->connector->getLogins('key'));
        Phake::verify($this->loginsProvider, Phake::times(1))->getLogins('key');
    }


}

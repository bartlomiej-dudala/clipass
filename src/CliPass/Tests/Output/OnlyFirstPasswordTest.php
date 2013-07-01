<?php

namespace CliPass\Test\Output;

use CliPass\Output\OnlyFirstPassword;
use Phake;

class OnlyFirstPasswordTest extends \PHPUnit_Framework_TestCase
{

    /** @var  OnlyFirstPassword */
    private $output;

    public function setUp()
    {
        $this->output = new OnlyFirstPassword();
    }

    /** @test */
    public function should_return_only_first_password()
    {
        $entry1 = Phake::mock('\CliPass\Response\Entry');
        $entry2 = Phake::mock('\CliPass\Response\Entry');

        Phake::when($entry1)->getPassword()->thenReturn('password');

        ob_start();
        $this->output->output(array($entry1, $entry2));
        $this->assertEquals('password', ob_get_contents());
        ob_end_clean();
    }

    /** @test */
    public function should_return_nothing_if_no_entry_has_been_found()
    {
        ob_start();
        $this->output->output(array());
        $this->assertEmpty(ob_get_contents());
        ob_end_clean();
    }
}

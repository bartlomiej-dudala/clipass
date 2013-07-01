<?php

namespace CliPass\Test\Output;

use CliPass\Output\GitCredentialHelper;
use CliPass\Response\Entry;
use Phake;

class GitCredentialHelperTest extends \PHPUnit_Framework_TestCase
{

    /** @var  GitCredentialHelper */
    private $output;

    public function setUp()
    {
        $this->output = new GitCredentialHelper();
    }

    /** @test */
    public function echo_should_contains_login_from_entry()
    {
        $entry = Phake::mock('\CliPass\Response\Entry');
        Phake::when($entry)->getLogin()->thenReturn('login');
        $parameters = array('login' => 'login-from-git');
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array($entry));
        $this->assertContains('username=login', ob_get_contents());
        ob_end_clean();
    }

    /** @test */
    public function echo_should_contains_password_from_entry()
    {
        $entry = Phake::mock('\CliPass\Response\Entry');
        Phake::when($entry)->getPassword()->thenReturn('password');
        $parameters = array('password' => 'password-from-git');
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array($entry));
        $this->assertContains('password=password', ob_get_contents());
        ob_end_clean();
    }

    /** @test */
    public function echo_should_contains_protocol_from_parameters()
    {
        $entry = Phake::mock('\CliPass\Response\Entry');
        $parameters = array('protocol' => 'my-protocol');
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array($entry));
        $this->assertContains('protocol=my-protocol', ob_get_contents());
        ob_end_clean();
    }

    /** @test */
    public function echo_should_contains_host_from_parameters()
    {
        $entry = Phake::mock('\CliPass\Response\Entry');
        $parameters = array('host' => 'host.local');
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array($entry));
        $this->assertContains('host=host.local', ob_get_contents());
        ob_end_clean();
    }


    /** @test */
    public function should_return_only_first_entry()
    {
        $entry1 = Phake::mock('\CliPass\Response\Entry');
        $entry2 = Phake::mock('\CliPass\Response\Entry');

        ob_start();
        $this->output->output(array($entry1, $entry2));
        ob_end_clean();

        Phake::verify($entry1, Phake::times(1))->getLogin();
        Phake::verify($entry2, Phake::never())->getLogin();
    }

    /** @test */
    public function result_should_be_well_formatted()
    {
        $entry = Phake::mock('\CliPass\Response\Entry');
        Phake::when($entry)->getLogin()->thenReturn('login');
        Phake::when($entry)->getPassword()->thenReturn('password');
        $parameters = array(
            'protocol' => 'https',
            'host' => 'host.local',
        );
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array($entry));
        $this->assertEquals("protocol=https\nhost=host.local\nusername=login\npassword=password\n\n", ob_get_contents());
        ob_end_clean();
    }

    /** @test */
    public function should_return_gitParameters_if_no_entry_has_been_found()
    {
        $parameters = array(
            'protocol' => 'https',
            'host' => 'host.local',
        );
        $this->output->setGitParameters($parameters);

        ob_start();
        $this->output->output(array());
        $this->assertEquals("protocol=https\nhost=host.local\n\n", ob_get_contents());
        ob_end_clean();
    }

}

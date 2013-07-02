<?php

namespace CliPass\Test;

use CliPass\Command;
use Phake;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    private $getOpt;
    private $stdIn;
    private $connector;
    private $outputFactory;

    /** @var Command */
    private $command;

    public function setUp()
    {
        $this->getOpt = Phake::mock('\Ulrichsg\Getopt');
        $this->stdIn = Phake::mock('\CliPass\Input\StdIn');
        $this->connector = Phake::mock('\CliPass\KeePassConnector');
        $this->outputFactory = Phake::mock('\CliPass\Output\Factory');

        $this->command = new Command($this->getOpt, $this->stdIn, $this->outputFactory, $this->connector);
    }

    /** @test */
    public function should_ask_for_hostname_in_gitCredentialHelper_mode()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('git-command' => 'get'));

        Phake::when($this->getOpt)->getOption('output-adapter')->thenReturn('git-credential-helper');

        Phake::when($this->getOpt)->getOption('git-command')->thenReturn('get');
        Phake::when($this->stdIn)->read()->thenReturn('host=host.local');
        Phake::when($this->connector)->getLogins(Phake::anyParameters())->thenReturn(array());

        $output = Phake::mock('\CliPass\Output\GitCredentialHelper');
        Phake::when($this->outputFactory)->build('GitCredentialHelper')->thenReturn($output);

        $this->command->execute();

        Phake::verify($this->connector, Phake::times(1))->getLogins('host.local');
    }

    /** @test */
    public function should_add_prefix_to_hostname_in_gitCredentialHelper_mode()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('git-command' => 'get'));

        Phake::when($this->getOpt)->getOption('output-adapter')->thenReturn('git-credential-helper');
        Phake::when($this->getOpt)->getOption('git-command')->thenReturn('get');
        Phake::when($this->getOpt)->getOption('key-prefix')->thenReturn('prefix-');
        Phake::when($this->stdIn)->read()->thenReturn('host=host.local');

        $output = Phake::mock('\CliPass\Output\GitCredentialHelper');
        Phake::when($this->outputFactory)->build('GitCredentialHelper')->thenReturn($output);

        $this->command->execute();

        Phake::verify($this->connector, Phake::times(1))->getLogins('prefix-host.local');
    }

    /** @test */
    public function should_attach_gitParameters_to_GitCredentialHelper_output()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('git-command' => 'get'));

        Phake::when($this->getOpt)->getOption('output-adapter')->thenReturn('git-credential-helper');
        Phake::when($this->getOpt)->getOption('git-command')->thenReturn('get');
        Phake::when($this->stdIn)->read()->thenReturn('host=host.local');

        $output = Phake::mock('\CliPass\Output\GitCredentialHelper');
        Phake::when($this->outputFactory)->build('GitCredentialHelper')->thenReturn($output);

        $this->command->execute();

        Phake::verify($output)->setGitParameters(array('host' => 'host.local'));
    }

    /** @test */
    public function should_do_nothing_in_gitCredentialHelper_mode_for_method_store()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('git-command' => 'store'));

        Phake::when($this->getOpt)->getOption('output-adapter')->thenReturn('git-credential-helper');
        Phake::when($this->getOpt)->getOption('git-command')->thenReturn('store');
        Phake::when($this->stdIn)->read()->thenReturn('host=host.local');

        $output = Phake::mock('\CliPass\Output\GitCredentialHelper');
        Phake::when($this->outputFactory)->build('GitCredentialHelper')->thenReturn($output);

        $this->command->execute();

        Phake::verifyNoInteraction($this->connector);
    }

    /** @test */
    public function should_ask_for_key_in_non_gitCredentialHelper_mode()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('key' => 'key'));
        Phake::when($this->getOpt)->getOption('key')->thenReturn('key');
        Phake::when($this->connector)->getLogins(Phake::anyParameters())->thenReturn(array());

        $output = Phake::mock('\CliPass\Output\OutputInterface');
        Phake::when($this->outputFactory)->build('OnlyFirstPassword')->thenReturn($output);

        $this->command->execute();

        Phake::verify($this->connector, Phake::times(1))->getLogins('key');
    }

    /** @test */
    public function should_add_prefix_to_hostname_in_non_gitCredentialHelper_mode()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('key' => 'key'));
        Phake::when($this->getOpt)->getOption('key')->thenReturn('key');
        Phake::when($this->getOpt)->getOption('key-prefix')->thenReturn('prefix-');

        $output = Phake::mock('\CliPass\Output\OutputInterface');
        Phake::when($this->outputFactory)->build('OnlyFirstPassword')->thenReturn($output);

        $this->command->execute();

        Phake::verify($this->connector, Phake::times(1))->getLogins('prefix-key');
    }

    /** @test */
    public function should_use_OnlyFirstPassword_adapter_by_defaults()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('key' => 'key'));
        $output = Phake::mock('\CliPass\Output\OutputInterface');
        Phake::when($this->outputFactory)->build('OnlyFirstPassword')->thenReturn($output);

        $this->command->execute();

        Phake::verify($output, Phake::times(1))->output(array());
    }

    /** @test */
    public function should_use_OnlyFirstPassword_adapter_when_it_is_called_in_command_line()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array('key' => 'key'));
        Phake::when($this->getOpt)->getOption('output-adapter')->thenReturn('only-first-password');

        $output = Phake::mock('\CliPass\Output\OutputInterface');
        Phake::when($this->outputFactory)->build('OnlyFirstPassword')->thenReturn($output);

        $this->command->execute();

        Phake::verify($output, Phake::times(1))->output(array());
    }

    /** @test */
    public function should_show_usage()
    {
        Phake::when($this->getOpt)->getOptions()->thenReturn(array());

        $this->command->execute();

        Phake::verify($this->getOpt, Phake::times(1))->showHelp();
    }
}

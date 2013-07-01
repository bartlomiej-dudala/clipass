<?php

namespace CliPass;

use Pimple;
use Symfony\Component\Console\Output\Output;
use Ulrichsg\Getopt;
use CliPass\Input\StdIn;
use CliPass\Output\Factory AS OutputFactory;

class Command
{
    /** @var \Ulrichsg\Getopt */
    protected $getopt;

    /** @var StdIn */
    protected $stdIn;
    
    /** @var  OutputFactory */
    protected $outputFactory;

    /** @var  KeePassConnector */
    protected $connector;

    /**
     * @param \Ulrichsg\Getopt $getopt
     */
    public function __construct(Getopt $getopt, StdIn $stdIn, OutputFactory $outputFactory, KeePassConnector $connector)
    {
        $this->getopt = $getopt;
        $this->stdIn = $stdIn;
        $this->outputFactory = $outputFactory;

        $this->connector = $connector;
    }

    public function execute()
    {
        $this->getopt->addOptions(array(
            array(null, 'key', Getopt::OPTIONAL_ARGUMENT),
            array(null, 'key-prefix', Getopt::OPTIONAL_ARGUMENT),
            array(null, 'output-adapter', Getopt::OPTIONAL_ARGUMENT),
            array(null, 'git-command', Getopt::OPTIONAL_ARGUMENT),
        ));

        $this->getopt->parse();
        if(!count($this->getopt->getOptions())) {
            $this->getopt->showHelp();

            return;
        }

        $adapter = $this->getopt->getOption('output-adapter');

        if($adapter === 'git-credential-helper') {
            $this->executeAsGitCredentialHelper($this->getopt->getOption('git-command'));
            return;
        }

        $key = $this->getopt->getOption('key-prefix') . $this->getopt->getOption('key');

        $entries = $this->connector->getLogins($key);

        $output = $this->outputFactory->build('OnlyFirstPassword');
        $output->output($entries);

    }

    /**
     * @param string $gitCommand
     */
    public function executeAsGitCredentialHelper($gitCommand)
    {
        if($gitCommand === 'store') {
            return;
        }

        $parameters = $this->parseGitInputParameters();

        $host = $this->getopt->getOption('key-prefix') . $parameters['host'];

        $entries = $this->connector->getLogins($host);

        $output = $this->outputFactory->build('GitCredentialHelper');
        $output->setGitParameters($parameters);
        $output->output($entries);
    }

    /**
     * @return array
     */
    private function parseGitInputParameters()
    {
        return parse_ini_string($this->stdIn->read());
    }
}

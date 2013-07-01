<?php

namespace CliPass\Output;

use CliPass\Output\OutputInterface;

class GitCredentialHelper implements OutputInterface
{
    protected $gitParameters;

    public function output($entries)
    {
        if(empty($entries)) {
            foreach($this->gitParameters AS $key => $value) {
                echo "{$key}={$value}\n";
            }
            echo "\n";
            return;
        }
        $entry = current($entries);
        $response = array(
            'protocol' =>  $this->getGitParameter('protocol'),
            'host' =>  $this->getGitParameter('host'),
            'username' => $entry->getLogin(),
            'password' => $entry->getPassword(),
        );
        foreach($response AS $key => $value) {
            echo "{$key}={$value}\n";
        }
        echo "\n";
    }

    private function getGitParameter($parameterName)
    {
        if(isset($this->gitParameters[$parameterName])) {
            return $this->gitParameters[$parameterName];
        }
        return '';
    }

    /**
     * @param mixed $gitParameters
     */
    public function setGitParameters($gitParameters)
    {
        $this->gitParameters = $gitParameters;
    }

    /**
     * @return mixed
     */
    public function getGitParameters()
    {
        return $this->gitParameters;
    }
}

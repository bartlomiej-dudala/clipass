<?php

namespace CliPass\Test\Output;


use CliPass\Output\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_create_correct_object()
    {
        $factory = new Factory();
        $class = $factory->build('GitCredentialHelper');

        $this->assertInstanceOf('\CliPass\Output\GitCredentialHelper', $class);
    }
}
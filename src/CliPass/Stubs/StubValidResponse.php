<?php

namespace CliPass\Stubs;

use CliPass\Response\ResponseInterface;

class StubValidResponse implements ResponseInterface
{
    private $id;

    public function validate()
    {
        return true;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getEntries()
    {
        return array();
    }
}
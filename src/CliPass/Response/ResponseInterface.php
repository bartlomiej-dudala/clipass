<?php

namespace CliPass\Response;

interface ResponseInterface
{
    /**
     * @return bool
     */
    public function validate();

    /**
     * @return string
     */
    public function getId();

    /**
     * @return array
     */
    public function getEntries();
}

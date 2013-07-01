<?php

namespace CliPass\Output;

use CliPass\Output\OutputInterface;
use CliPass\Response\Entry;

class OnlyFirstPassword implements OutputInterface
{
    public function output($entries)
    {
        if(!count($entries)) {
            return;
        }
        /** @var Entry $entry */
        $entry = current($entries);
        echo $entry->getPassword();
    }
}

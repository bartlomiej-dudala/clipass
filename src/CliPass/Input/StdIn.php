<?php

namespace CliPass\Input;

class StdIn
{
    /**
     * @return string
     */
    public function read()
    {
        $stdin = fopen('php://stdin', 'r');
        $contents = '';
        while (!feof($stdin)) {
            $contents .= fread($stdin, 8192);
        }
        fclose($stdin);

        return $contents;
    }
}

<?php

namespace CliPass\Output;


class Factory 
{
    public function build($adapterName)
    {
        $className = '\CliPass\Output\\' . $adapterName;
        return new $className();
    }
}
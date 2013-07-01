<?php

namespace CliPass;

use Buzz\Browser;

class KeePassConnector
{
    /**
     * @var Associator
     */
    protected $associator;

    /** @var \CliPass\LoginsProvider  */
    protected $loginsProvider;

    /**
     * @param \Buzz\Browser $browser
     */
    public function __construct(Associator $associator, LoginsProvider $loginsProvider)
    {
        $this->associator = $associator;
        $this->loginsProvider = $loginsProvider;
    }

    public function getLogins($name)
    {
        $this->associator->associate();
        return $this->loginsProvider->getLogins($name);
    }
}

<?php

namespace CliPass\Response;

use CliPass\Crypt;
use CliPass\Identity;

class Entry
{
    protected $cryptedLogin;

    protected $cryptedPassword;

    protected $cryptedUuid;

    protected $cryptedName;

    protected $iv;

    /**
     * @var Identity
     */
    protected $identity;

    /**
     * @var Crypt
     */
    protected $crypt;

    public function __construct(Identity $identity, Crypt $crypt)
    {
        $this->identity = $identity;
        $this->crypt = $crypt;
    }

    public function setCryptedLogin($cryptedLogin)
    {
        $this->cryptedLogin = $cryptedLogin;
    }

    public function getLogin()
    {
        return $this->decrypt($this->cryptedLogin);
    }

    public function setCryptedName($cryptedName)
    {
        $this->cryptedName = $cryptedName;
    }

    public function getName()
    {
        return $this->decrypt($this->cryptedName);
    }

    public function setCryptedPassword($cryptedPassword)
    {
        $this->cryptedPassword = $cryptedPassword;
    }

    public function getPassword()
    {

        return $this->decrypt($this->cryptedPassword);
    }

    public function setCryptedUuid($cryptedUuid)
    {
        $this->cryptedUuid = $cryptedUuid;
    }

    public function getUuid()
    {
        return $this->decrypt($this->cryptedUuid);
    }

    protected function decrypt($data)
    {
        return $this->crypt->decrypt($data, $this->identity->getKey(), $this->iv);
    }

    /**
     * @param mixed $iv
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }

    /**
     * @return mixed
     */
    public function getIv()
    {
        return $this->iv;
    }
}
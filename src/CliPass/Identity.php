<?php

namespace CliPass;

use Gaufrette\Filesystem;
use Gaufrette\Adapter;

class Identity
{
    const STORAGE_KEY = '.clipass.identity';
    /**
     * @var string
     */
    private $keyName;

    /**
     * @var string
     */
    private $key;

    /**
     * @var \Gaufrette\Filesystem
     */
    private $storage;

    /**
     * @param \Gaufrette\Adapter $storageAdapter
     */
    public function __construct(Adapter $storageAdapter)
    {
        $this->storage = new Filesystem($storageAdapter);
        $this->loadIdentityFromStorage();
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    public function saveInStorage()
    {
        $identityData = base64_encode($this->keyName) . "\n" . base64_encode($this->key);
        $this->storage->write(self::STORAGE_KEY, $identityData);
    }

    private function loadIdentityFromStorage()
    {
        if(!$this->storage->getAdapter()->exists(self::STORAGE_KEY)) {
            return;
        }
        $lines = explode("\n", $this->storage->read(self::STORAGE_KEY));

        if(count($lines) >= 2) {
            $this->keyName = base64_decode($lines[0]);
            $this->key = base64_decode($lines[1]);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->key) && is_null($this->keyName);
    }

}

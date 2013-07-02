<?php

namespace CliPass;

use CliPass\StringEncoder\Base64Encoder;
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
    protected $storage;

    /** @var  Base64Encoder */
    protected $base64Encoder;


    /**
     * @param \Gaufrette\Adapter $storageAdapter
     */
    public function __construct(Adapter $storageAdapter, Base64Encoder $base64Encoder)
    {
        $this->storage = new Filesystem($storageAdapter);
        $this->base64Encoder = $base64Encoder;

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
        if($this->storage->getAdapter()->exists(self::STORAGE_KEY)) {
            $this->storage->getAdapter()->delete(self::STORAGE_KEY);
        }

        $identityData = $this->base64Encoder->encode($this->keyName) . "\n"
            . $this->base64Encoder->encode($this->key);

        $this->storage->write(self::STORAGE_KEY, $identityData);
    }

    private function loadIdentityFromStorage()
    {
        if(!$this->storage->getAdapter()->exists(self::STORAGE_KEY)) {
            return;
        }
        $lines = explode("\n", $this->storage->read(self::STORAGE_KEY));

        if(count($lines) >= 2) {
            $this->keyName = $this->base64Encoder->decode($lines[0]);
            $this->key = $this->base64Encoder->decode($lines[1]);
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

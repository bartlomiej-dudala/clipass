<?php

namespace CliPass;

class Crypt
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $iv;

    /**
     * @param $encrypt
     * @return string
     */
    public function encrypt($encrypt, $key, $iv)
    {
        $encrypt = $this->addPKCS7Padding($encrypt);

        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $encrypt);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $encrypted;
    }

    /**
     * @param $encrypt
     * @return string
     */
    private function addPKCS7Padding($encrypt)
    {
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        if (($pad = $block - (strlen($encrypt) % $block)) < $block) {
            $encrypt .= str_repeat(chr($pad), $pad);
            return $encrypt;
        }
        return $encrypt;
    }

    /**
     * @param $decrypt
     * @return string
     */
    public function decrypt($decrypt, $key, $iv)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $decrypt);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        $decrypted = $this->removePKCS7Padding($decrypted);
        return trim($decrypted);
    }

    /**
     * @param $decrypted
     * @return string
     */
    private function removePKCS7Padding($decrypted)
    {
        $block = $this->getBlockSize();
        $pad = ord($decrypted[($len = strlen($decrypted)) - 1]);
        if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $decrypted)) {
            $decrypted = substr($decrypted, 0, strlen($decrypted) - $pad);
            return $decrypted;
        }
        return $decrypted;
    }

    /**
     * @param string $iv
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }

    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
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
     * @return string
     */
    public function generateKey()
    {
        $key = '';
        for($i=0; $i<16; $i++) {
            $key.= chr(rand(0,255));
        }

        return $key;
    }

    /**
     * @return string
     */
    public function generateIv()
    {
        return mcrypt_create_iv($this->getBlockSize(), MCRYPT_RAND);
    }

    /**
     * @return int
     */
    private function getBlockSize()
    {
        return mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    }
}
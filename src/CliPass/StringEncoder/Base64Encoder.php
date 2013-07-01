<?php

namespace CliPass\StringEncoder;


class Base64Encoder 
{
    /**
     * @param $string
     * @return string
     */
    public function encode($string)
    {
        return base64_encode($string);
    }

    /**
     * @param $string
     * @return string
     */
    public function decode($string)
    {
        return base64_decode($string);
    }
}
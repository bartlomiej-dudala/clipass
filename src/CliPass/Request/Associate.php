<?php

namespace CliPass\Request;

class Associate extends AbstractRequest
{
    /**
     * @return string
     */
    public function getJSONEncodedParams()
    {
        $iv = $this->crypt->generateIv();
        $key = $this->crypt->generateKey();
        $this->identity->setKey($key);

        $verifier = $this->crypt->encrypt($this->base64Encoder->encode($iv), $key, $iv);

        $params = array(
            'RequestType' => 'associate',
            'Key' => $this->base64Encoder->encode($key),
            'Nonce' => $this->base64Encoder->encode($iv),
            'Verifier' => $this->base64Encoder->encode($verifier),
            'Id' => null,
        );

        return json_encode($params);
    }
}
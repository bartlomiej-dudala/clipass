<?php

namespace CliPass\Request;

class TestAssociate extends AbstractRequest
{
    /**
     * @return string
     */
    public function getJSONEncodedParams()
    {
        $iv = $this->crypt->generateIv();
        $key = $this->identity->getKey();
        $verifier = $this->crypt->encrypt($this->base64Encoder->encode($iv), $key, $iv);

        $params = array(
            'RequestType' => 'test-associate',
            'Key' => $this->base64Encoder->encode($key),
            'Nonce' => $this->base64Encoder->encode($iv),
            'Verifier' => $this->base64Encoder->encode($verifier),
            'Id' => $this->identity->getKeyName(),
        );

        return json_encode($params);
    }
}
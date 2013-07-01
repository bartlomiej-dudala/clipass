<?php

namespace CliPass\Request;

class GetLogins extends AbstractRequest
{
    protected $searchKey;

    /**
     * @param string $searchKey
     */
    public function setSearchKey($searchKey)
    {
        $this->searchKey = $searchKey;
    }

    /**
     * @return string
     */
    public function getSearchKey()
    {
        return $this->searchKey;
    }

    /**
     * @return string
     */
    public function getJSONEncodedParams()
    {
        $iv = $this->crypt->generateIv();
        $key = $this->identity->getKey();

        $verifier = $this->crypt->encrypt($this->base64Encoder->encode($iv), $key, $iv);

        $params = array(
            'RequestType' => 'get-logins',
            'Key' => base64_encode($key),
            'Nonce' => $this->base64Encoder->encode($iv),
            'Url' => $this->base64Encoder->encode($this->crypt->encrypt($this->searchKey, $key, $iv)),
            'SubmitUrl' => $this->base64Encoder->encode($this->crypt->encrypt($this->searchKey, $key, $iv)),
            'Verifier' => $this->base64Encoder->encode($verifier),
            'Id' => $this->identity->getKeyName(),
        );

        return json_encode($params);
    }
}
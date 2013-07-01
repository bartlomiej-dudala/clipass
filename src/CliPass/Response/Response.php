<?php

namespace CliPass\Response;

use Buzz\Message\Response AS BuzzResponse;
use CliPass\Crypt;
use CliPass\Identity;
use CliPass\StringEncoder\Base64Encoder;

class Response implements ResponseInterface
{
    /** @var \Buzz\Message\Response */
    protected $response;

    /** @var Identity */
    protected $identity;

    /** @var Crypt */
    protected $crypt;

    /** @var  Base64Encoder */
    protected $base64Encoder;

    /** @var array */
    protected $entries = array();

    protected $contentData;

    protected $successful;

    /**
     * @param BuzzResponse $response
     * @param Identity $identity
     * @param Crypt $crypt
     * @param Base64Encoder $base64Encoder
     */
    public function __construct(BuzzResponse $response, Identity $identity, Crypt $crypt, Base64Encoder $base64Encoder)
    {
        $this->response = $response;
        $this->identity = $identity;
        $this->crypt = $crypt;
        $this->base64Encoder = $base64Encoder;

        if(!$response->isSuccessful()) {
            $this->successful = false;
        }
        else {
            $this->successful = true;
            $this->contentData = json_decode($this->response->getContent(), true);
        }

        $entries = $this->getFieldFromResponse('Entries', false);
        if(isSet($entries)) {
            foreach($entries AS $entryRow) {

                $entry = new Entry($this->identity, $this->crypt);
                $entry->setCryptedLogin(base64_decode($entryRow['Login']));
                $entry->setCryptedPassword(base64_decode($entryRow['Password']));
                $entry->setCryptedUuid(base64_decode($entryRow['Uuid']));
                $entry->setCryptedName(base64_decode($entryRow['Name']));
                $entry->setIv($this->getFieldFromResponse('Nonce', true));

                $this->entries[] = $entry;
            }
        }
    }

    public function validate()
    {
        if(!$this->successful) {
            return false;
        }

        $nonce = $this->getFieldFromResponse('Nonce', true);
        $verifier = $this->getFieldFromResponse('Verifier', true);

        if(is_null($nonce) || is_null($verifier)) {
            return false;
        }

        $verifier = $this->base64Encoder->decode($this->crypt->decrypt($verifier, $this->identity->getKey(), $nonce));

        return ($verifier === $nonce);
    }

    private function getFieldFromResponse($fieldName, $isBase64Encoded)
    {
        if(!is_null($this->contentData) && isset($this->contentData[$fieldName])) {
            if($isBase64Encoded) {
                return $this->base64Encoder->decode($this->contentData[$fieldName]);
            }
            else {
                return $this->contentData[$fieldName];
            }
        }
    }

    public function getId()
    {
        return $this->getFieldFromResponse('Id', false);
    }


    public function getEntries()
    {
        return $this->entries;
    }
}

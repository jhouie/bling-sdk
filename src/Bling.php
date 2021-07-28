<?php

namespace Bling;

class Bling
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @param string $apiKey
     * @param string $baseUri
     *
     * @throws \ErrorException
     */
    public function __construct(string $apiKey, string $baseUri)
    {
        if (empty($apiKey)) {
            throw new \ErrorException("Empty API key provided.");
        }

        $this->client = new Client($apiKey, $baseUri);
    }
}
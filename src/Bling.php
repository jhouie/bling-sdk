<?php

namespace Bling;

use Bling\Repositories\Categories;
use Bling\Repositories\Products;
use ErrorException;

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
     * @throws ErrorException
     */
    public function __construct(string $apiKey, string $baseUri)
    {
        if (empty($apiKey)) {
            throw new ErrorException("Empty API key provided.");
        }

        $this->client = new Client($apiKey, $baseUri);
    }

    /**
     * @return Products
     */
    public function products(): Products
    {
        return new Products($this->client);
    }

    /**
     * @return Categories
     */
    public function categories(): Categories
    {
        return new Categories($this->client);
    }
}

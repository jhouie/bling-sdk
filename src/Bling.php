<?php

namespace Bling;

use Bling\Repositories\Categories;
use Bling\Repositories\Products;
use InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function __construct(string $apiKey, string $baseUri)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException("Empty API key provided.");
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

<?php

namespace Bling;

use Bling\Repositories\Categories;
use Bling\Repositories\Products;
use Bling\Repositories\Warehouses;
use InvalidArgumentException;

class Bling
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @param string $apiKey
     * @param array  $options
     */
    public function __construct(string $apiKey, array $options = [])
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException("Empty API key provided.", 2);
        }

        $this->client = new Client($apiKey, $options);
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

    /**
     * @return Warehouses
     */
    public function warehouses(): Warehouses
    {
        return new Warehouses($this->client);
    }
}

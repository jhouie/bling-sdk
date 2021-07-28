<?php

namespace Bling\Repositories;

use Bling\Client;

class Products
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $url = "produtos/json";

        $request = $this->client->get($url);

        if (! $request) {
            return [];
        }

        if (! empty($request['produtos'])) {
            return array_map(function ($item) {
                return $item['produto'];
            }, $request['produtos']);
        }

        return [];
    }

    /**
     * @param string $productCode
     *
     * @return false|Product
     */
    public function find(string $productCode)
    {
        $response = $this->client->get("produto/{$productCode}/json/");

        if ($response && ! empty($response['produtos'])) {
            return array_shift($response['produtos'])['produto'];
        }

        return false;
    }
}

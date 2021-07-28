<?php

namespace Bling\Repositories;

use Bling\Client;
use Spatie\ArrayToXml\ArrayToXml;

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
     * @return array|false
     */
    public function find(string $productCode)
    {
        $response = $this->client->get("produto/{$productCode}/json/");

        if ($response && ! empty($response['produtos'])) {
            return array_shift($response['produtos'])['produto'];
        }

        return false;
    }

    /**
     * @param array $product
     *
     * @return false|array
     */
    public function create(array $product)
    {
        $response = $this->client->post('produto/json/', [
            'xml' => $this->createXmlString($product),
        ]);

        return $response ? $response['produtos'][0]['produto'] : false;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function createXmlString(array $data): string
    {
        if (! empty($data['images'])) {
            foreach ($data['images'] as $key => $url) {
                $data['imagens']['__custom:url:' . $key] = $url;
            }
        }

        return ArrayToXml::convert($data, 'produto');
    }
}

<?php

namespace Bling\Repositories;

use Bling\Client;
use InvalidArgumentException;
use Spatie\ArrayToXml\ArrayToXml;

class Products
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var bool
     */
    protected bool $includeImages = false;

    /**
     * @var bool
     */
    protected bool $includeStock = false;

    /**
     * @var string
     */
    protected string $storeId = '';

    /**
     * @var array
     */
    private array $requestOptions = [];

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var string[]
     */
    protected static array $availableFilters = [
        'dataInclusao',
        'dataAlteracao',
        'dataAlteracaoLoja',
        'dataInclusaoLoja',
        'situacao',
        'tipo',
    ];

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
        $this->parseRequestOptions()
             ->parseRequestFilters();

        $url = "produtos/json";

        $request = $this->client->get(
            $url,
            $this->requestOptions
        );

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
        $this->parseRequestOptions();

        $response = $this->client->get(
            "produto/{$productCode}/json/",
            $this->requestOptions
        );

        if ($response && ! empty($response['produtos'])) {
            return array_shift($response['produtos'])['produto'];
        }

        return false;
    }

    /**
     * @param array $product
     *
     * @return array|false
     */
    public function create(array $product)
    {
        return $this->save($product);
    }

    /**
     * @param string $productCode
     * @param array  $product
     *
     * @return array|false
     */
    public function update(string $productCode, array $product)
    {
        return $this->save($product, $productCode);
    }

    /**
     * @param string $productCode
     *
     * @return bool
     */
    public function delete(string $productCode): bool
    {
        return (bool) $this->client->delete("produto/{$productCode}/json/");
    }

    /**
     * @param array $filters
     *
     * @return Products
     *
     * @throws InvalidArgumentException
     */
    public function setFilters(array $filters): Products
    {
        foreach ($filters as $key => $value) {
            if (! in_array($key, static::$availableFilters)) {
                throw new InvalidArgumentException("The given filter '{$key}' is invalid.");
            }

            $this->filters[$key] = $value;
        }

        return $this;
    }

    /**
     * @return Products
     */
    public function withImages(): Products
    {
        $this->includeImages = true;

        return $this;
    }

    /**
     * @return Products
     */
    public function withStock(): Products
    {
        $this->includeStock = true;

        return $this;
    }

    /**
     * @param array  $product
     * @param string $productCode
     *
     * @return array|false
     */
    private function save(array $product, string $productCode = '')
    {
        $url = ! empty($productCode) ? "produto/{$productCode}/json/" : 'produto/json/';

        $response = $this->client->post($url, [
            'xml' => $this->createXmlString($product),
        ]);

        return $response ? $response['produtos'][0]['produto'] : false;
    }

    /**
     * @return Products
     */
    protected function parseRequestOptions(): Products
    {
        if ($this->includeImages) {
            $this->requestOptions['imagem'] = 'S';
        }

        if ($this->includeStock) {
            $this->requestOptions['estoque'] = 'S';
        }

        if (! empty($this->storeId)) {
            $this->requestOptions['loja'] = $this->storeId;
        }

        return $this;
    }

    /**
     * @return Products
     */
    protected function parseRequestFilters(): Products
    {
        if (! empty($this->filters)) {
            $filters = [];

            foreach ($this->filters as $filter => $value) {
                $filters[] = $filter . '[' . $value . ']';
            }

            $this->requestOptions['filters'] = implode(';', $filters);
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function createXmlString(array $data): string
    {
        if (! empty($data['imagens'])) {
            $images = $data['imagens'];

            unset($data['imagens']);

            foreach ($images as $key => $url) {
                $data['imagens']['__custom:url:' . $key] = $url;
            }
        }

        return ArrayToXml::convert($data, 'produto');
    }
}

<?php

namespace Bling\Repositories;

class Products extends BaseRepository
{
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
     * @var string
     */
    protected string $resourceNamePlural = 'produtos';

    /**
     * @var string
     */
    protected string $resourceNameSingular = 'produto';

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
     * @param array  $data
     * @param string $resourceId
     *
     * @return array|false
     */
    protected function save(array $data, string $resourceId = '')
    {
        $url = ! empty($resourceId) ? "produto/{$resourceId}/json/" : 'produto/json/';

        $response = $this->client->post($url, [
            'xml' => $this->createXmlString($data),
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
     * @inheritDoc
     */
    protected function createXmlString(array $data): string
    {
        if (! empty($data['imagens'])) {
            $images = $data['imagens'];

            unset($data['imagens']);

            foreach ($images as $key => $url) {
                $data['imagens']['__custom:url:' . $key] = $url;
            }
        }

        return parent::createXmlString($data);
    }
}

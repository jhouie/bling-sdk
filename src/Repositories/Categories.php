<?php

namespace Bling\Repositories;

use Spatie\ArrayToXml\ArrayToXml;

class Categories extends BaseRepository
{
    /**
     * @return array
     */
    public function all(): array
    {
        $url = "categorias/json";

        $request = $this->client->get($url);

        if (! $request) {
            return [];
        }

        if (! empty($request['categorias'])) {
            return array_map(function ($item) {
                return $item['categoria'];
            }, $request['categorias']);
        }

        return [];
    }

    /**
     * @param int $categoryId
     *
     * @return array|false
     */
    public function find(int $categoryId)
    {
        $response = $this->client->get(
            "categoria/{$categoryId}/json/",
            $this->requestOptions
        );

        if ($response && ! empty($response['categorias'])) {
            return array_shift($response['categorias'])['categoria'];
        }

        return false;
    }

    /**
     * @param array $category
     *
     * @return array|false
     */
    public function create(array $category)
    {
        return $this->save($category);
    }

    /**
     * @param array $category
     * @param int   $categoryId
     *
     * @return array|false
     */
    public function update(array $category, int $categoryId)
    {
        return $this->save($category, $categoryId);
    }

    /**
     * @param array    $category
     * @param int|null $categoryId
     *
     * @return array|false
     */
    private function save(array $category, int $categoryId = null)
    {
        $params = [ 'xml' => $this->createXmlString($category) ];

        if (! is_null($categoryId)) {
            $response = $this->client->put("categoria/{$categoryId}/json/", $params);
        } else {
            $response = $this->client->post('categoria/json/', $params);
        }

        return $response ? $response['categorias'][0][0]['categoria'] : false;
    }

    /**
     * @inheritDoc
     */
    protected function createXmlString(array $data): string
    {
        return ArrayToXml::convert($data, 'categoria');
    }
}

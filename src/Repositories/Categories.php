<?php

namespace Bling\Repositories;

class Categories extends BaseRepository
{
    /**
     * @var string
     */
    protected string $resourceNamePlural = 'categorias';

    /**
     * @var string
     */
    protected string $resourceNameSingular = 'categoria';

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
}

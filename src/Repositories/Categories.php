<?php

namespace Bling\Repositories;

use Bling\Entities\Category;

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
     * @return array
     */
    public function all(): array
    {
        $result = [];

        if ($categories = parent::all()) {
            foreach ($categories as $category) {
                $result[] = $this->parseCategory($category);
            }
        }

        return $result;
    }

    /**
     * @param string $resourceId
     *
     * @return Category|false
     */
    public function find(string $resourceId)
    {
        if ($response = parent::find($resourceId)) {
            return $this->parseCategory($response);
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return Category|false
     */
    public function create(array $data)
    {
        if ($response = parent::create($data)) {
            return $this->parseCategory($response);
        }

        return false;
    }

    /**
     * @param array  $data
     * @param string $resourceId
     *
     * @return Category|false
     */
    public function update(array $data, string $resourceId)
    {
        if ($response = parent::update($data, $resourceId)) {
            return $this->parseCategory($response);
        }

        return false;
    }

    /**
     * @param array $categoryData
     *
     * @return Category
     */
    private function parseCategory(array $categoryData): Category
    {
        return (new Category())->setId($categoryData['id'])
                               ->setDescription($categoryData['descricao'])
                               ->setParentId($categoryData['idCategoriaPai']);
    }
}

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
        return array_map(function ($category) {
            return $this->parseCategory($category);
        }, parent::all());
    }

    /**
     * @param string $resourceId
     *
     * @return Category|false
     */
    public function find(string $resourceId)
    {
        return $this->parseCategory(parent::find($resourceId));
    }

    /**
     * @param array $data
     *
     * @return Category|false
     */
    public function create(array $data)
    {
        return $this->parseCategory(parent::create($data));
    }

    /**
     * @param array  $data
     * @param string $resourceId
     *
     * @return Category|false
     */
    public function update(array $data, string $resourceId)
    {
        return $this->parseCategory(parent::update($data, $resourceId));
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

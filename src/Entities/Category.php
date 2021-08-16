<?php

namespace Bling\Entities;

use Spatie\ArrayToXml\ArrayToXml;

class Category
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var int|null
     */
    private ?int $parentId = null;

    /**
     * @param int $id
     *
     * @return Category
     */
    public function setId(int $id): Category
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return Category
     */
    public function setDescription(string $description): Category
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param int $parentId
     *
     * @return Category
     */
    public function setParentId(int $parentId): Category
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function toXmlString(): string
    {
        $data = [
            'descricao' => $this->description,
        ];

        if (! is_null($this->id)) {
            $data['id'] = $this->id;
        }

        if (! is_null($this->parentId)) {
            $data['idcategoriapai'] = $this->parentId;
        }

        return ArrayToXml::convert($data,
            'categoria',
            true,
            'UTF-8',
            '1.0',
            [ 'formatOutput' => true ]
        );
    }
}

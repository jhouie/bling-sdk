<?php

namespace Bling\Repositories;

use Bling\Entities\Warehouse;

class Warehouses extends BaseRepository
{
    /**
     * @var string
     */
    protected string $resourceNamePlural = 'depositos';

    /**
     * @var string
     */
    protected string $resourceNameSingular = 'deposito';

    /**
     * @var bool
     */
    protected bool $active = false;

    /**
     * @var bool
     */
    protected bool $inactive = false;

    /**
     * @return Warehouses
     */
    protected function parseRequestOptions(): Warehouses
    {
        if ($this->active) {
            $this->filters['situacao'] = 'A';
        }

        if ($this->inactive) {
            $this->filters['situacao'] = 'I';
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return array_map(function ($warehouse) {
            return $this->parseWarehouse($warehouse);
        }, parent::all());
    }

    /**
     * @param string $resourceId
     *
     * @return Warehouse
     */
    public function find(string $resourceId): Warehouse
    {
        return $this->parseWarehouse(parent::find($resourceId));
    }

    /**
     * @param array $data
     *
     * @return Warehouse
     */
    public function create(array $data): Warehouse
    {
        return $this->parseWarehouse(parent::create($data));
    }

    /**
     * @param array  $data
     * @param string $resourceId
     *
     * @return Warehouse
     */
    public function update(array $data, string $resourceId): Warehouse
    {
        return $this->parseWarehouse(parent::update($data, $resourceId));
    }

    /**
     * @param array $warehouseData
     *
     * @return Warehouse
     */
    private function parseWarehouse(array $warehouseData): Warehouse
    {
        return (new Warehouse())->setId($warehouseData['id'])
                               ->setDescription($warehouseData['descricao'])
                               ->setActive($warehouseData['situacao'])
                               ->setDefault($warehouseData['depositoPadrao'])
                               ->setDisregardBalance($warehouseData['desconsiderarSaldo']);
    }
}

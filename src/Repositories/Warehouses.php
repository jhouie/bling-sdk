<?php

namespace Bling\Repositories;

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
}

<?php

namespace Bling\Entities;

class Warehouse
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @var bool
     */
    private bool $isDefault = false;

    /**
     * @var bool
     */
    private bool $isActive = false;

    /**
     * @var bool
     */
    private bool $disregardBalance = false;

    /**
     * @param int $id
     *
     * @return Warehouse
     */
    public function setId(int $id): Warehouse
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $description
     *
     * @return Warehouse
     */
    public function setDescription(string $description): Warehouse
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $active
     *
     * @return Warehouse
     */
    public function setActive(string $active): Warehouse
    {
        $this->isActive = $active === 'Ativo' || $active === 'A';

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param string $default
     *
     * @return Warehouse
     */
    public function setDefault(string $default): Warehouse
    {
        $this->isDefault = $default === 'true';

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param string $disregardBalance
     *
     * @return Warehouse
     */
    public function setDisregardBalance(string $disregardBalance): Warehouse
    {
        $this->disregardBalance = $disregardBalance === 'true';

        return $this;
    }

    /**
     * @return bool
     */
    public function disregardBalance(): bool
    {
        return $this->disregardBalance;
    }
}

<?php

namespace Bling\Repositories;

use Bling\Client;
use InvalidArgumentException;

abstract class BaseRepository
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var array
     */
    protected array $filters = [];

    /**
     * @var array
     */
    protected array $requestOptions = [];

    /**
     * @var string[]
     */
    protected static array $availableFilters = [];

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function setFilters(array $filters): BaseRepository
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
     * @return BaseRepository
     */
    protected function parseRequestFilters(): BaseRepository
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
    abstract protected function createXmlString(array $data): string;
}

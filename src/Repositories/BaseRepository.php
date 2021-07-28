<?php

namespace Bling\Repositories;

use Bling\Client;
use InvalidArgumentException;
use Spatie\ArrayToXml\ArrayToXml;

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
     * @var string
     */
    protected string $resourceNamePlural = '';

    /**
     * @var string
     */
    protected string $resourceNameSingular = '';

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function all(): array
    {
        $this->parseRequestOptions()
             ->parseRequestFilters();

        $url = "{$this->resourceNamePlural}/json";

        $request = $this->client->get($url, $this->requestOptions);

        if (! $request) {
            return [];
        }

        if (! empty($request[$this->resourceNamePlural])) {
            return array_map(function ($item) {
                return $item[$this->resourceNameSingular];
            }, $request[$this->resourceNamePlural]);
        }

        return [];
    }

    public function find(string $resourceId)
    {
        $this->parseRequestOptions();

        $url = "{$this->resourceNameSingular}/{$resourceId}/json";

        $response = $this->client->get($url, $this->requestOptions);

        if ($response && ! empty($response[$this->resourceNamePlural])) {
            return array_shift($response[$this->resourceNamePlural])[$this->resourceNameSingular];
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return array|false
     */
    public function create(array $data)
    {
        return $this->save($data);
    }

    /**
     * @param array  $data
     * @param string $resourceId
     *
     * @return array|false
     */
    public function update(array $data, string $resourceId)
    {
        return $this->save($data, $resourceId);
    }

    /**
     * @param array  $data
     * @param string $resourceId
     *
     * @return array|false
     */
    protected function save(array $data, string $resourceId = '')
    {
        $params = [ 'xml' => $this->createXmlString($data) ];

        if (! empty($resourceId)) {
            $response = $this->client->put(
                "{$this->resourceNameSingular}/{$resourceId}/json/",
                $params
            );
        } else {
            $response = $this->client->post(
                "{$this->resourceNameSingular}/json/",
                $params
            );
        }

        if ($response) {
            $resource = array_shift($response[$this->resourceNamePlural][0]);

            return $resource[$this->resourceNameSingular];
        }

        return false;
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
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->client->getErrorMessage();
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->client->getErrorCode();
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
     * @return $this
     */
    protected function parseRequestOptions(): BaseRepository
    {
        return $this;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function createXmlString(array $data): string
    {
        return ArrayToXml::convert($data, $this->resourceNameSingular);
    }
}

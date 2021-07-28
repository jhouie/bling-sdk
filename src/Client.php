<?php

namespace Bling;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Uri;

final class Client
{
    /**
     * @var string
     */
    protected string $apiKey = '';

    /**
     * @var GuzzleClient
     */
    protected GuzzleClient $client;

    /**
     * @param string $apiKey
     * @param string $baseUri
     */
    public function __construct(string $apiKey, string $baseUri)
    {
        $this->apiKey = $apiKey;

        $this->client = new GuzzleClient([
            'base_uri' => new Uri($baseUri),
        ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array|mixed
     */
    public function request(string $method, string $uri, array $options = [])
    {
        $method = strtoupper($method);

        if ($method == 'GET' || $method == 'DELETE') {
            $options['query'] = array_merge([ 'apikey' => $this->apiKey ], $options);
        } elseif ($method == 'POST' || $method == 'PUT') {
            $options['form_params'] = array_merge([ 'apikey' => $this->apiKey ], $options);
        }

        try {
            $request  = $this->client->request($method, $uri, $options);
            $response = json_decode($request->getBody(), true);

            return $response['retorno'];
        } catch (ClientException $ce) {
            $response = json_decode($ce->getResponse()->getBody(), true);

            return $response['retorno']['erros'];
        } catch (GuzzleException $ge) {
            $this->errorCode = 99;
            $this->errorReason = $ge->getMessage();

            return false;
        }
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array|mixed
     */
    public function get(string $uri, array $params = [])
    {
        return $this->request('GET', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array|mixed
     */
    public function post(string $uri, array $params)
    {
        return $this->request('POST', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array|mixed
     */
    public function put(string $uri, array $params)
    {
        return $this->request('PUT', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return false|mixed
     */
    public function delete(string $uri, array $params = [])
    {
        return $this->request('DELETE', $uri, $params);
    }
}
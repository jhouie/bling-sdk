<?php

namespace Bling;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
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
     * @var int
     */
    protected int $errorCode = 0;

    /**
     * @var string
     */
    protected string $errorMessage = '';

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
            $request = $this->client->request($method, $uri, $options);
            $response = json_decode($request->getBody(), true);
            $errors = ! empty($response['retorno']['erros']) ? $response['retorno']['erros'] : [];

            if (! empty($errors)) {
                $this->handleErrors($errors);

                return false;
            }

            return $response['retorno'];
        } catch (ClientException $ce) {
            $response = json_decode($ce->getResponse()->getBody(), true);

            $this->handleErrors($response['retorno']['erros']);

            return $response['retorno']['erros'];
        } catch (GuzzleException $ge) {
            $this->errorCode = 99;
            $this->errorMessage = $ge->getMessage();

            return false;
        }
    }

    /**
     * @param array $errors
     *
     * @return void
     */
    private function handleErrors(array $errors): void
    {
        $error = ! empty($errors[0]) ? $errors[0] : $errors;

        if (array_key_exists('erro', $error)) {
            $code = $error['erro']['cod'];
            $message = $error['erro']['msg'];
        } else {
            $code = (int) array_key_first($error);
            $message = $error[$code];
        }

        $this->errorCode = $code;
        $this->errorMessage = $message;
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

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}

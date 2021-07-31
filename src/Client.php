<?php

namespace Bling;

use Bling\Exceptions\UnauthorizedException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

final class Client
{
    /**
     * @var string
     */
    protected string $apiKey = '';

    /**
     * @var HttpClient
     */
    protected HttpClient $client;

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
     * @param array  $options
     */
    public function __construct(string $apiKey, array $options)
    {
        $clientOptions = array_merge([ 'base_uri' => 'https://bling.com.br/Api/v2/' ], $options);

        $this->apiKey = $apiKey;

        $this->client = new HttpClient($clientOptions);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array|mixed
     *
     * @throws UnauthorizedException
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
            $errors = $response['retorno']['erros']['erro'];

            $this->handleErrors($response['retorno']['erros']);

            if ($ce->getResponse()->getStatusCode() === 401) {
                throw new UnauthorizedException($errors['msg'], $errors['cod']);
            }

            return false;
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
     *
     * @throws UnauthorizedException
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
     *
     * @throws UnauthorizedException
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
     *
     * @throws UnauthorizedException
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
     *
     * @throws UnauthorizedException
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

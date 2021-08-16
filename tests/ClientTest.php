<?php

namespace Tests;

use Bling\Client;
use Bling\Exceptions\BlingException;
use Bling\Exceptions\UnauthorizedException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function canUseHttpVerbsCorrectly()
    {
        $mockClient = $this->createPartialMock(Client::class, ['request']);
        $mockClient->expects($this->atLeast(4))
                   ->method('request')
                   ->withConsecutive(
                       ['GET', '/', []],
                       ['POST', '/',['foo' => 'bar']],
                       ['PUT', '/', ['foo' => 'bar']],
                       ['DELETE', '/', []]
                   );

        $mockClient->get('/', []);
        $mockClient->post('/', ['foo' => 'bar']);
        $mockClient->put('/', ['foo' => 'bar']);
        $mockClient->delete('/');
    }

    /** @test */
    public function canHandleSuccessfullRequest()
    {
        $handler = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode([
                'retorno' => [
                    'categorias' => [
                        [ 'categoria' => [ 'teste' ] ],
                    ],
                ],
            ])),
        ]));

        $client = new Client('apiKey', [ 'handler' => $handler ]);

        $response = $client->get('/');

        $this->assertEquals(200, $client->getStatusCode());
        $this->assertEquals([ [ 'categoria' => [ 'teste' ] ] ], $response['categorias']);
    }

    public function errorDataProvider()
    {
        return [
            'Invalid API Key'       => [
                401,
                file_get_contents(__DIR__ . '/Mocks/Errors/Unauthorized.json'),
                UnauthorizedException::class,
                3,
                'API Key invalida',
            ],
            'General Error Format'  => [
                200,
                file_get_contents(__DIR__ . '/Mocks/Errors/GeneralFormat.json'),
                BlingException::class,
                14,
                'A informacao desejada nao foi encontrada',
            ],
            'Specific Error Format' => [
                200,
                file_get_contents(__DIR__ . '/Mocks/Errors/SpecificFormat.json'),
                BlingException::class,
                94,
                'Já existe uma categoria de mesmo nível e pai com a descrição informada',
            ],
            'Server Error'          => [
                500,
                file_get_contents(__DIR__ . '/Mocks/Errors/ServerError.json'),
                GuzzleException::class,
                500,
                'Erro interno',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider errorDataProvider
     */
    public function canHandleErrorsCorrectly($statusCode, $response, $expectedException, $expectedExceptionCode, $expectedExceptionMessage)
    {
        $handler = HandlerStack::create(new MockHandler([
            new Response($statusCode, [], $response),
        ]));

        $client = new Client('apiKey', [ 'handler' => $handler ]);

        $this->expectException($expectedException);
        $this->expectExceptionCode($expectedExceptionCode);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $client->get('/');
    }
}

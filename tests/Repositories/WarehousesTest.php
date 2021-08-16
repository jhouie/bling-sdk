<?php

namespace Tests\Repositories;

use Bling\Bling;
use Bling\Entities\Warehouse;
use Bling\Repositories\Warehouses;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WarehousesTest extends TestCase
{
    /** @test */
    public function canListAllWarehouses()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Warehouses/All')),
        ]);

        $response = $service->all();

        $this->assertContainsOnlyInstancesOf(Warehouse::class, $response);

        $this->assertEquals('Test Warehouse 1', $response[0]->getDescription());
        $this->assertEquals('Test Warehouse 2', $response[1]->getDescription());

        $this->assertTrue($response[0]->isDefault());
        $this->assertTrue($response[0]->isActive());
        $this->assertFalse($response[1]->isActive());
        $this->assertTrue($response[1]->disregardBalance());
    }

    /** @test */
    public function canSetRequestFilters()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Warehouses/All')),
            new Response(200, [], $this->jsonMock('Warehouses/All')),
        ]);

        $service->active()->all();

        $this->assertEquals("situacao[A]", $service->getRequestFilters());

        $service->inactive()->all();

        $this->assertEquals("situacao[I]", $service->getRequestFilters());
    }

    /** @test */
    public function canGetASingleWarehouse()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Warehouses/Single')),
        ]);

        $warehouse = $service->find('123');

        $this->assertInstanceOf(Warehouse::class, $warehouse);
    }

    /** @test */
    public function canCreateAWarehouse()
    {
        $service = $this->mockService([
            new Response(201, [], $this->jsonMock('Warehouses/Create')),
        ]);

        $warehouse = $service->create([
            "descricao"          => "Test Warehouse",
            "desconsiderarSaldo" => false,
            "depositoPadrao"     => false,
            "situacao"           => 'A',
        ]);

        $this->assertInstanceOf(Warehouse::class, $warehouse);

        $this->assertEquals(123, $warehouse->getId());
        $this->assertEquals('Test Warehouse', $warehouse->getDescription());
        $this->assertTrue($warehouse->isActive());
        $this->assertFalse($warehouse->isDefault());
        $this->assertFalse($warehouse->disregardBalance());
    }

    /** @test */
    public function canUpdateAWarehouse()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Warehouses/Update')),
        ]);

        $warehouse = $service->update([
            "descricao" => "Updated Warehouse",
        ], 123);

        $this->assertInstanceOf(Warehouse::class, $warehouse);
        $this->assertEquals('Updated Warehouse', $warehouse->getDescription());
    }

    /**
     * @param $queue
     *
     * @return Warehouses
     */
    private function mockService($queue): Warehouses
    {
        $handler = HandlerStack::create(new MockHandler($queue));
        $client = new Bling('apiKey', [ 'handler' => $handler ]);

        return $client->warehouses();
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function jsonMock(string $filename): string
    {
        return file_get_contents(__DIR__ . "/../Mocks/$filename.json");
    }
}

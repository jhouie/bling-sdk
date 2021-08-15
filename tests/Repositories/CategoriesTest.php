<?php

namespace Tests\Repositories;

use Bling\Bling;
use Bling\Entities\Category;
use Bling\Repositories\Categories;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CategoriesTest extends TestCase
{
    /** @test */
    public function listAllCategories()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Categories/All')),
        ]);

        $response = $service->all();

        $this->assertEquals('Test Category 1', $response[0]->getDescription());
        $this->assertEquals('Test Category 2', $response[1]->getDescription());
    }

    /** @test */
    public function getSingleCategory()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Categories/Single')),
        ]);

        $category = $service->find('1234567');

        $this->assertInstanceOf(Category::class, $category);

        $this->assertEquals('1234567', $category->getId());
        $this->assertEquals('Test Category', $category->getDescription());
        $this->assertEquals('1234566', $category->getParentId());
    }

    /** @test */
    public function createCategory()
    {
        $service = $this->mockService([
            new Response(201, [], $this->jsonMock('Categories/Create')),
        ]);

        $category = $service->create([
            "descricao"      => "Test Category",
            "idCategoriaPai" => 0,
        ]);

        $this->assertInstanceOf(Category::class, $category);

        $this->assertEquals(12345, $category->getId());
        $this->assertEquals('Test Category', $category->getDescription());
        $this->assertEquals(0, $category->getParentId());
    }

    /** @test */
    public function updateCategory()
    {
        $service = $this->mockService([
            new Response(200, [], $this->jsonMock('Categories/Update')),
        ]);

        $category = $service->update([
            "descricao"      => "Updated Category",
            "idCategoriaPai" => 12344,
        ], 12345);

        $this->assertInstanceOf(Category::class, $category);

        $this->assertEquals(12345, $category->getId());
        $this->assertEquals('Updated Category', $category->getDescription());
        $this->assertEquals(12344, $category->getParentId());
    }

    /**
     * @param $queue
     *
     * @return Categories
     */
    private function mockService($queue): Categories
    {
        $handler = HandlerStack::create(new MockHandler($queue));
        $client = new Bling('apiKey', [ 'handler' => $handler ]);

        return $client->categories();
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

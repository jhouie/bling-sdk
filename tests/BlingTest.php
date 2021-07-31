<?php

namespace Tests;

use Bling\Bling;
use Bling\Repositories\Categories;
use Bling\Repositories\Products;
use Bling\Repositories\Warehouses;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BlingTest extends TestCase
{
    public function repositoryDataProvider(): array
    {
        return [
            [ 'categories', Categories::class ],
            [ 'products', Products::class ],
            [ 'warehouses', Warehouses::class ],
        ];
    }

    /**  @test */
    public function itValidatesEmptyApiKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage("Empty API key provided.");

        $bling = new Bling('');
    }

    /**
     * @test
     * @dataProvider repositoryDataProvider
     */
    public function itCanGetRepositoriesCorrectly($name, $class)
    {
        $bling = new Bling('apiKey');

        $this->assertInstanceOf($class, $bling->$name());
    }
}

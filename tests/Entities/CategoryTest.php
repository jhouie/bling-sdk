<?php

namespace Tests\Entities;

use Bling\Entities\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /** @test */
    public function xmlStringEncode()
    {
        $category = (new Category())->setId(1)
                                    ->setDescription('Empty Description')
                                    ->setParentId(1234);

        $expectedXmlResult = file_get_contents(__DIR__ . '/Category.xml');

        $this->assertEquals($expectedXmlResult, $category->toXmlString());
    }
}

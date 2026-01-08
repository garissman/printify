<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyCatalog;
use Garissman\Printify\PrintifyImage;
use Garissman\Printify\PrintifyProducts;
use Garissman\Printify\PrintifyShop;
use Garissman\Printify\Structures\Product;
use Garissman\Printify\Structures\Shop;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductsTest extends TestCase
{
    const IMAGE = 'https://codeuture.com/android-icon-192x192.png';

    const HANDLE = 'https://test.com/test';

    public $printify_products = null;

    protected Shop $shop;

    protected function setUp(): void
    {
        parent::setUp();
        $printifyShop = new PrintifyShop($this->api);
        $shops = $printifyShop->all();

        // Find shop by name if configured, otherwise use first shop
        if (property_exists(Credentials::class, 'shop_name') && Credentials::$shop_name) {
            $this->shop = $shops->firstWhere('title', Credentials::$shop_name) ?? $shops->first();
        } else {
            $this->shop = $shops->first();
        }

        $this->printify_products = new PrintifyProducts($this->api, $this->shop);
    }

    public function test_products_all()
    {
        $products = $this->printify_products->all();
        $this->assertTrue($products instanceof Collection || $products instanceof LengthAwarePaginator);
        $this->assertTrue((count($products) > 0));
        $product = $products instanceof LengthAwarePaginator ? $products->items()[0] : $products[0];
        $this->assertInstanceOf(Product::class, $product);
        $structure = [
            'id',
            'title',
            'description',
            'tags',
            'options',
            'variants',
            'images',
            'created_at',
            'updated_at',
            'visible',
            'is_locked',
            'blueprint_id',
            'user_id',
            'shop_id',
            'print_provider_id',
            'print_areas',
            'sales_channel_properties',
        ];
        $this->assertArrayStructure($structure, $product->toArray());
        $this->assertNotEmpty($product->tags);
    }

    public function test_product_pagination()
    {
        $products = $this->printify_products->all(['limit' => 1]);
        $this->assertInstanceOf(LengthAwarePaginator::class, $products);
        $this->assertGreaterThanOrEqual(1, $products->lastPage());
        $this->assertCount(1, $products);
    }

    // public function testProductPaginateAll()
    // {
    //     //TODO create 2 products
    //     $products = $this->printify_products->all(['limit' => 1, 'paginate' => true]);
    // }

    public function test_get_product()
    {
        // Get the first product from the list
        $products = $this->printify_products->all(['limit' => 1]);
        $this->assertGreaterThanOrEqual(1, count($products));

        $firstProduct = $products instanceof LengthAwarePaginator ? $products->items()[0] : $products[0];
        $product = $this->printify_products->find($firstProduct->id);
        $this->assertInstanceOf(Product::class, $product);
        $structure = [
            'id',
            'title',
            'description',
            'tags',
            'options',
            'variants',
            'images',
            'created_at',
            'updated_at',
            'visible',
            'is_locked',
            'blueprint_id',
            'user_id',
            'shop_id',
            'print_provider_id',
            'print_areas',
            'sales_channel_properties',
        ];
        $this->assertArrayStructure($structure, $product->toArray());
    }

    public function test_create_product()
    {
        $product = $this->_create_product();
        $structure = [
            'id',
            'title',
            'description',
            'tags',
            'options',
            'variants',
            'images',
            'created_at',
            'updated_at',
            'visible',
            'is_locked',
            'blueprint_id',
            'user_id',
            'shop_id',
            'print_provider_id',
            'print_areas',
            'sales_channel_properties',
        ];
        $this->assertArrayStructure($structure, $product->toArray());
        $this->printify_products->delete($product->id);
    }

    public function test_update_product()
    {
        $product = $this->_create_product();
        $title = 'Updated PHPUnit Test Product';
        $update_data = [
            'title' => $title,
        ];
        $product = $this->printify_products->update($product->id, $update_data);
        $this->assertEquals($title, $product->title);
        $this->printify_products->delete($product->id);
    }

    public function test_delete_product()
    {
        $product = $this->_create_product();
        $response = $this->printify_products->delete($product->id);
        $this->assertTrue($response);
    }

    public function test_publish()
    {
        $product = $this->_create_product();
        $response = $this->printify_products->publish($product->id);
        $this->assertTrue($response);
        $this->printify_products->delete($product->id);
    }

    public function test_publishing_succeeded()
    {
        $product = $this->_create_product();
        $response = $this->printify_products->publishing_succeeded($product->id, self::HANDLE);
        $this->assertTrue($response);
        $this->printify_products->delete($product->id);
    }

    public function test_unpublish()
    {
        $product = $this->_create_product();
        $response = $this->printify_products->publish($product->id);
        $this->assertTrue($response);
        $response = $this->printify_products->publishing_succeeded($product->id, self::HANDLE);
        $this->assertTrue($response);
        $response = $this->printify_products->unpublish($product->id);
        $this->assertTrue($response);
        $this->printify_products->delete($product->id);
    }

    private function _create_product(): Product
    {
        $printify_catalog = new PrintifyCatalog($this->api);
        $blueprints = $printify_catalog->all();
        $blueprint = $blueprints[0];
        $print_providers = $printify_catalog->print_providers($blueprint->id);
        $print_provider = $print_providers[0];
        $create_data = [
            'title' => 'PHPUnit Test Product',
            'description' => 'A Product Created by PHP Tests',
            'blueprint_id' => $blueprint->id,
            'print_provider_id' => $print_provider->id,
            'variants' => [],
            'print_areas' => [],
        ];
        $print_provider_variants = $printify_catalog->print_provider_variants($blueprint->id, $print_provider->id);
        $printify_image = new PrintifyImage($this->api);
        $variant_ids = [];
        foreach ($print_provider_variants as $variant) {
            $create_data['variants'][] = [
                'id' => $variant->id,
                'price' => mt_rand(100, 4000),
                'is_enabled' => mt_rand(0, 1) ? true : false,
            ];
            $variant_ids[] = $variant->id;
        }
        $image = $printify_image->create('logo.png', self::IMAGE);
        $create_data['print_areas'][] = [
            'variant_ids' => $variant_ids,
            'placeholders' => [
                [
                    'position' => 'front',
                    'images' => [
                        [
                            'id' => $image->id,
                            'x' => 0,
                            'y' => 0,
                            'scale' => 1,
                            'angle' => 0,
                        ],
                    ],
                ],
            ],
        ];
        $product = $this->printify_products->create($create_data);
        $this->assertInstanceOf(Product::class, $product);

        return $product;
    }
}

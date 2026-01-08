<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyCatalog;
use Garissman\Printify\PrintifyImage;
use Garissman\Printify\PrintifyOrders;
use Garissman\Printify\PrintifyProducts;
use Garissman\Printify\PrintifyShop;
use Garissman\Printify\Structures\Order\LineItem;
use Garissman\Printify\Structures\Order\Order;
use Garissman\Printify\Structures\Product;
use Garissman\Printify\Structures\Shop;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrdersTest extends TestCase
{
    const IMAGE = 'https://codeuture.com/android-icon-192x192.png';

    public $printify_orders = null;

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

        $this->printify_orders = new PrintifyOrders($this->api, $this->shop);
    }

    public function test_orders_all()
    {
        $orders = $this->printify_orders->all();
        $this->assertTrue($orders instanceof Collection || $orders instanceof LengthAwarePaginator);

        if (count($orders) > 0) {
            $order = $orders instanceof LengthAwarePaginator ? $orders->items()[0] : $orders->first();
            $this->assertInstanceOf(Order::class, $order);
            $structure = [
                'id',
                'address_to',
                'line_items',
                'total_price',
                'total_shipping',
                'total_tax',
                'status',
                'shipping_method',
                'created_at',
            ];
            $this->assertArrayStructure($structure, $order->toArray());
        }
    }

    public function test_get_order()
    {
        $orders = $this->printify_orders->all(['limit' => 1]);

        if (count($orders) > 0) {
            $firstOrder = $orders instanceof LengthAwarePaginator ? $orders->items()[0] : $orders->first();
            $order = $this->printify_orders->find($firstOrder->id);
            $this->assertInstanceOf(Order::class, $order);
            $structure = [
                'id',
                'address_to',
                'line_items',
                'total_price',
                'total_shipping',
                'total_tax',
                'status',
                'shipping_method',
                'created_at',
            ];
            $this->assertArrayStructure($structure, $order->toArray());
        } else {
            $this->markTestSkipped('No orders available to test');
        }
    }

    public function test_create_order()
    {
        $order = $this->_create_order();
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotEmpty($order->id);
        $structure = [
            'id',
            'address_to',
            'line_items',
            'total_price',
            'total_shipping',
            'total_tax',
            'status',
            'shipping_method',
            'created_at',
        ];
        $this->assertArrayStructure($structure, $order->toArray());

        // Try to cancel the test order (may fail if status doesn't allow)
        try {
            $this->printify_orders->cancel($order->id);
        } catch (\Exception $e) {
            // Order may not be cancellable depending on status
        }
    }

    private function _create_order(): Order
    {
        $product = $this->_create_product();
        $enabledVariants = array_filter($product->variants, fn($v) => $v['is_enabled'] ?? false);
        if (empty($enabledVariants)) {
            $enabledVariants = $product->variants;
        }
        $variant = $enabledVariants[array_rand($enabledVariants)];
        $variant_id = $variant['id'];

        $create_data = [
            'label' => 'Test Order ' . mt_rand(1000, 9999),
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant_id,
                    'quantity' => 1,
                ],
            ],
            'shipping_method' => 1,
            'send_shipping_notification' => false,
            'address_to' => [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'phone' => '1234567890',
                'country' => 'US',
                'region' => 'CA',
                'address1' => '123 Test Street',
                'address2' => '',
                'city' => 'Los Angeles',
                'zip' => '90001',
            ],
        ];

        return $this->printify_orders->create($create_data);
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
        $printify_products = new PrintifyProducts($this->api, $this->shop);
        $product = $printify_products->create($create_data);
        $this->assertInstanceOf(Product::class, $product);

        return $product;
    }

    private function _generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

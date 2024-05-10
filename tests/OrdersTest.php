<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\Collection;
use Garissman\Printify\PrintifyCatalog;
use Garissman\Printify\PrintifyImage;
use Garissman\Printify\PrintifyOrders;
use Garissman\Printify\PrintifyProducts;
use Garissman\Printify\Structures\Order\LineItem;
use Garissman\Printify\Structures\Order\Order;
use Garissman\Printify\Structures\Order\Shipment;
use Garissman\Printify\Structures\Product;
use Garissman\Printify\Tests\TestCase;

class OrdersTest extends TestCase
{
    const IMAGE = 'https://artistrepublik.com/img/brand/logo_dark.png';
    public $printify_orders = null;

    protected function setUp()
    {
        parent::setUp();
        $this->printify_orders = new PrintifyOrders($this->api, Credentials::$shop_id);
    }

    // public function testOrdersAll()
    // {
    //     $order = $this->_create_order();
    //     $orders = $this->printify_orders->all();
    //     $this->assertInstanceOf(Collection::class, $orders);
    //     $this->assertTrue((count($orders) > 0));
    //     $order = $orders[count($orders) - 1];
    //     $this->assertInstanceOf(Order::class, $order);
    //     $structure = [
    //         'id',
    //         'address_to',
    //         'line_items',
    //         'metadata',
    //         'total_price',
    //         'total_shipping',
    //         'total_tax',
    //         'status',
    //         'shipping_method',
    //         'shipments',
    //         'created_at',
    //         'sent_to_production_at',
    //         'fulfilled_at'
    //     ];
    //     $this->assertArrayStructure($structure, $order->toArray());
    //     $this->assertInstanceOf(LineItem::class, $order->line_items[0]);
    //     $this->printify_orders->cancel($order->id);
    // }

    // public function testOrdersPagination()
    // {
    //     $order1 = $this->_create_order();
    //     $order2 = $this->_create_order();
    //     $orders = $this->printify_orders->all(['limit' => 1]);
    //     $this->assertTrue($orders->last_page > 1);
    //     $this->assertCount(1, $orders);
    //     // $this->printify_orders->cancel($order1->id);
    //     // $this->printify_orders->cancel($order2->id);
    // }

    // public function testGetOrder()
    // {
    //     $order = $this->_create_order();
    //     $this->assertInstanceOf(Order::class, $order);
    //     $structure = [
    //         'id',
    //         'title',
    //         'description',
    //         'tags',
    //         'options',
    //         'variants',
    //         'images',
    //         'created_at',
    //         'updated_at',
    //         'visible',
    //         'is_locked',
    //         'blueprint_id',
    //         'user_id',
    //         'shop_id',
    //         'print_provider_id',
    //         'print_areas',
    //         'sales_channel_properties'
    //     ];
    //     $this->assertArrayStructure($structure, $order->toArray());
    // }

    private function _create_order(): Order
    {
        $product = $this->_create_product();
        $variant_id = $product->variants[mt_rand(0, (count($product->variants) - 1))]['id'];
        $create_data = [
            'label' => mt_rand(0, 10).mt_rand(0, 20),
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant_id,
                    'quantity' => mt_rand(1, 10)
                ]
            ],
            'shipping_method' => 1,
            'send_shipping_notification' => true,
            'address_to' => [
                'first_name' => $this->_generateRandomString(),
                'last_name' => $this->_generateRandomString(),
                'email' => $this->_generateRandomString().'@fakeemail.com',
                'phone' => null,
                'country' => 'US',
                'region' => 'CA',
                'address1' => $this->_generateRandomString(),
                'address2' => null,
                'city' => $this->_generateRandomString(),
                'zip' => (string) mt_rand(10000, 99999)
            ]
        ];
        $order_id = $this->printify_orders->create($create_data);
        return $this->printify_orders->find($order_id);
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
            'print_areas' => []
        ];
        $print_provider_variants = $printify_catalog->print_provider_variants($blueprint->id, $print_provider->id);
        $printify_image = new PrintifyImage($this->api);
        $variant_ids = [];
        foreach ($print_provider_variants as $variant) {
            $create_data['variants'][] = [
                'id' => $variant->id,
                'price' => mt_rand(100, 4000),
                'is_enabled' => mt_rand(0, 1) ? true : false
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
                            'angle' => 0
                        ]
                    ]
                ]
            ]
        ];
        $printify_products = new PrintifyProducts($this->api, Credentials::$shop_id);
        $product = $printify_products->create($create_data);
        $this->assertInstanceOf(Product::class, $product);
        return $product;
    }

    private function _generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

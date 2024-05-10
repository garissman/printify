<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\Collection;
use Garissman\Printify\PrintifyShop;
use Garissman\Printify\Structures\Shop;
use Garissman\Printify\Tests\TestCase;

class ShopTest extends TestCase
{
    public $printify_shop = null;

    protected function setUp()
    {
        parent::setUp();
        $this->printify_shop = new PrintifyShop($this->api);
    }

    public function testShopsAll()
    {
        $shops = $this->printify_shop->all();
        $this->assertInstanceOf(Collection::class, $shops);
        $this->assertCount(1, $shops);
        $shop = $shops[0];
        $this->assertInstanceOf(Shop::class, $shop);
        $this->assertArrayHasKey('id', $shop->toArray());
    }

    //TODO test to disconnect a shop
}

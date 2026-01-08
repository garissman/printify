<?php

namespace Garissman\Printify\Tests;

use Garissman\Printify\PrintifyShop;
use Illuminate\Support\Collection;
use Garissman\Printify\Structures\Shop;

class ShopTest extends TestCase
{
    public $printify_shop = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->printify_shop = new PrintifyShop($this->api);
    }

    public function test_shops_all()
    {
        $shops = $this->printify_shop->all();
        $this->assertInstanceOf(Collection::class, $shops);
        $this->assertGreaterThanOrEqual(1, $shops->count());
        $shop = $shops->first();
        $this->assertInstanceOf(Shop::class, $shop);
        $this->assertArrayHasKey('id', $shop->toArray());
    }

    // TODO test to disconnect a shop
}

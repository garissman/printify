<?php

namespace Garissman\Printify;

use Garissman\Printify\Structures\Shop;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Config;


class Printify
{
    public function __construct(private Container $container)
    {
        $this->printify_api = new PrintifyApiClient(Config::get('printify.api_token'));
    }

    public function catalog(): PrintifyCatalog
    {
        return new PrintifyCatalog($this->printify_api);
    }

    public function order(Shop $shop = null): PrintifyOrders
    {
        if (!$shop) {
            $shop = $this->shop()->all()[0];
        }
        return new PrintifyOrders($this->printify_api, $shop);
    }

    public function shop(): PrintifyShop
    {
        return new PrintifyShop($this->printify_api);
    }

    public function product(Shop $shop = null): PrintifyProducts
    {
        if (!$shop) {
            $shop = $this->shop()->all()[0];
        }
        return new PrintifyProducts($this->printify_api, $shop);
    }

    public function webhook(Shop $shop = null): PrintifyWebhooks
    {
        if (!$shop) {
            $shop = $this->shop()->all()[0];
        }
        return new PrintifyWebhooks($this->printify_api, $shop);
    }

    public function image(): PrintifyImage
    {
        return new PrintifyImage($this->printify_api);
    }
}

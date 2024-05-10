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

    public function shop(): PrintifyShop
    {
        return new PrintifyShop($this->printify_api);
    }

    public function order(Shop $shop): PrintifyOrders
    {
        return new PrintifyOrders($this->printify_api, $shop);
    }

    public function product(Shop $shop): PrintifyProducts
    {
        return new PrintifyProducts($this->printify_api, $shop);
    }

    public function webhook(Shop $shop): PrintifyWebhooks
    {
        return new PrintifyWebhooks($this->printify_api, $shop);
    }

    public function image(): PrintifyImage
    {
        return new PrintifyImage($this->printify_api);
    }
}

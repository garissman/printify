<?php

namespace Printify;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Config;


class Printify
{
    public function __construct(private Container $container)
    {
        dd(Config::get('printify.api_token'));
        $this->printify_api = new PrintifyApiClient(Config::get('printify.api_token'));
    }

    public function catalog(): PrintifyCatalog
    {
        return new PrintifyCatalog($this->printify_api);
    }

    public function image(): PrintifyImage
    {
        return new PrintifyImage($this->printify_api);
    }

    public function order(): PrintifyOrders
    {
        return new PrintifyOrders($this->printify_api);
    }

    public function product(): PrintifyProducts
    {
        return new PrintifyProducts($this->printify_api);
    }

    public function shop(): PrintifyShop
    {
        return new PrintifyShop($this->printify_api);
    }

    public function webhook(): PrintifyWebhooks
    {
        return new PrintifyWebhooks($this->printify_api);
    }
}

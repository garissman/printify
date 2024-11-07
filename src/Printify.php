<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Shop;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Config;


class Printify
{
    private PrintifyApiClient $client;

    public function __construct()
    {
        $this->client = new PrintifyApiClient(Config::get('printify.api_token'));
    }

    public function catalog(): PrintifyCatalog
    {
        return new PrintifyCatalog($this->client);
    }

    public function order(Shop $shop = null): PrintifyOrders
    {
        if (!$shop) {
            $shop = $this->shop()->all()[0];
        }
        return new PrintifyOrders($this->client, $shop);
    }

    public function shop(): PrintifyShop
    {
        return new PrintifyShop($this->client);
    }

    /**
     * @throws Exception
     */
    public function product(Shop $shop = null): PrintifyProducts
    {
        if (!$shop) {
            $shop = $this->shop()->all()->first();
        }
        return new PrintifyProducts($this->client, $shop);
    }

    public function webhook(Shop $shop = null): PrintifyWebhooks
    {
        if (!$shop) {
            $shop = $this->shop()->all()[0];
        }
        return new PrintifyWebhooks($this->client, $shop);
    }

    public function image(): PrintifyImage
    {
        return new PrintifyImage($this->printify_api);
    }
}

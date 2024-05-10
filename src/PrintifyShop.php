<?php

namespace Garissman\Printify;

use Garissman\Printify\Structures\Shop;
use Illuminate\Support\Collection;

class PrintifyShop extends PrintifyBaseEndpoint
{
    protected $_structure = Shop::class;

    public function all(array $query_options = []): Collection
    {
        $items = $this->_api_client->doRequest('shops.json');
        return $this->collectStructure($items);
    }

    public function disconnect($id): bool
    {
        $this->_api_client->doRequest('shops/' . $id . '/connection.json', 'DELETE');
        return $this->_api_client->status_code === 200;
    }
}

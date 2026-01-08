<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Shop;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrintifyShop extends PrintifyBaseEndpoint
{
    protected string $structure = Shop::class;

    /**
     * @throws Exception
     */
    public function all(array $query_options = []): LengthAwarePaginator|Collection
    {
        $items = $this->client->doRequest('shops.json');
        return $this->collectStructure($items->json(),$query_options);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function disconnect(int $id): bool
    {
        return $this->client
            ->doRequest('shops/' . $id . '/connection.json', 'DELETE')
            ->ok();
    }


}

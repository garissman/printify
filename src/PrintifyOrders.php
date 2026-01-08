<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Order\Order;
use Garissman\Printify\Structures\Shop;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrintifyOrders extends PrintifyBaseEndpoint
{
    public ?string $shop_id = null;
    protected string $structure = Order::class;

    public function __construct(PrintifyApiClient $client, Shop $shop)
    {
        parent::__construct($client);
        if (!$shop) {
            throw new Exception('A shop is required to use the orders module');
        }
        $this->shop_id = $shop->id;
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function all(array $query_options = []): LengthAwarePaginator|Collection
    {
        if (empty($query_options) || !array_key_exists('limit', $query_options)) {
            $query_options['limit'] = 10;
        }
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders.json', 'GET', $query_options);
        return $this->collectStructure($response->json(), $query_options);
    }

    /**
     * Get order details by id
     *
     * @param string $id
     * @return Order
     * @throws ConnectionException
     * @throws RequestException
     */
    public function find(string $id): Order
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders/' . $id . '.json');
        return Order::from($response->json());
    }

    /**
     * Submit an order
     *
     * @param array $data
     * @return Order
     * @throws ConnectionException
     * @throws RequestException
     */
    public function create(array $data): Order
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders.json', 'POST', $data);
        return Order::from($response->json());
    }

    /**
     * Send an existing order to production
     *
     * @param string $id
     * @return Order
     * @throws ConnectionException
     * @throws RequestException
     */
    public function send_to_production(string $id): Order
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders/' . $id . '/send_to_production.json', 'POST');
        return Order::from($response->json());
    }

    /**
     * Calculate the shipping cost of an order
     *
     * @param array $data
     * @return array
     * @throws ConnectionException
     * @throws RequestException
     */
    public function calculate_shipping(array $data): array
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders/shipping.json', 'POST', $data);
        return $response->json();
    }

    /**
     * Cancel an order
     *
     * @param string $id
     * @return Order
     * @throws ConnectionException
     * @throws RequestException
     */
    public function cancel(string $id): Order
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders/' . $id . '/cancel.json', 'POST');
        return Order::from($response->json());
    }

    /**
     * Submit an express order (Printify Express - expedited delivery)
     *
     * @param array $data
     * @return Order
     * @throws ConnectionException
     * @throws RequestException
     */
    public function createExpress(array $data): Order
    {
        $response = $this->client->doRequest('shops/' . $this->shop_id . '/orders/express.json', 'POST', $data);
        return Order::from($response->json());
    }
}

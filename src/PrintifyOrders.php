<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Order\Order;

class PrintifyOrders extends PrintifyBaseEndpoint
{
    public $shop_id = null;
    protected $_structure = Order::class;

    public function __construct(PrintifyApiClient $api_client, $shop_id)
    {
        parent::__construct($api_client);
        if (!$shop_id) {
            throw new Exception('A shop id is required to use the products module');
        }
        $this->shop_id = $shop_id;
    }

    public function all(array $query_options = []): Collection
    {
        if (empty($query_options) || !array_key_exists('limit', $query_options)) {
            $query_options['limit'] = 10;
        }
        if (isset($query_options['paginate']) && $query_options['paginate']) {
            $this->_api_client->paginate = true;
        }
        $query = PrintifyApiClient::formatQuery($query_options);
        $items = $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders.json'.$query);
        return $this->collectStructure($items['data']);
    }

    /**
     * Get order details by id
     *
     * @param string $id
     * @return Order
     */
    public function find($id): Order
    {
        $item = $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders/'.$id.'.json');
        return new Order($item);
    }

    /**
     * Submit an order
     *
     * @param array $data
     * @return string - The order id
     */
    public function create(array $data): string
    {
        $response = $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders.json', 'POST', $data);
        return $response['id'];
    }

    /**
     * Send an existing order to production
     *
     * @param string $id
     * @return Order
     */
    public function send_to_production($id): Order
    {
        $item = $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders/'.$id.'/send_to_production.json', 'POST');
        return new Order($item);
    }

    /**
     * Calculate the shipping cost of an order
     *
     * @param array $data
     * @return array
     */
    public function calculate_shipping(array $data): array
    {
        return $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders/shipping.json', 'POST', $data);
    }

    /**
     * Cancel an order
     *
     * @param string $id
     * @return Order
     */
    public function cancel($id): Order
    {
        $item = $this->_api_client->doRequest('shops/'.$this->shop_id.'/orders/'.$id.'/cancel.json', 'POST');
        return new Order($item);
    }
}

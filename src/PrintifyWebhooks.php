<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Webhook;
use Illuminate\Support\Collection;

class PrintifyWebhooks extends PrintifyBaseEndpoint
{
    public $shop_id = null;
    protected $_structure = Webhook::class;

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
        $items = $this->_api_client->doRequest('shops/' . $this->shop_id . '/webhooks.json');
        return $this->collectStructure($items);
    }

    /**
     * Retrieve a single webhook
     *
     * @param string $id
     * @return Event
     */
    public function find(string $id): Webhook
    {
        $item = $this->_api_client->doRequest('shops/' . $this->shop_id . '/webhooks/' . $id . '.json');
        return new Webhook($item);
    }

    /**
     * Create a webhook
     *
     * @param string $event - AKA topic
     * @param string $url
     * @return boolean
     */
    public function create(string $event, string $url): Webhook
    {
        $data = [
            'topic' => $event,
            'url' => $url
        ];
        $item = $this->_api_client->doRequest('shops/' . $this->shop_id . '/webhooks.json', 'POST', $data);
        return new Webhook($item);
    }

    /**
     * Modify a webhook URL
     *
     * @param string $id
     * @param string $url
     * @return boolean
     */
    public function update(string $id, string $url): Webhook
    {
        $data = [
            'url' => $url
        ];
        $item = $this->_api_client->doRequest('shops/' . $this->shop_id . '/webhooks/' . $id . '.json', 'PUT', $data);
        return new Webhook($item);
    }

    /**
     * Delete a webhook
     *
     * @param string $id
     * @return boolean
     */
    public function delete(string $id): bool
    {
        $this->_api_client->doRequest('shops/' . $this->shop_id . '/webhooks/' . $id . '.json', 'DELETE');
        return $this->_api_client->status_code === 200;
    }

}

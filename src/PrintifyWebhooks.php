<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Shop;
use Garissman\Printify\Structures\Webhook;
use Garissman\Printify\Structures\Webhooks\EventsEnum;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;

class PrintifyWebhooks extends PrintifyBaseEndpoint
{
    protected string $structure = Webhook::class;

    public function __construct(PrintifyApiClient $client, public Shop $shop)
    {
        parent::__construct($client);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function all(array $query_options = []): Collection
    {
        $items = $this->client->doRequest('shops/' . $this->shop->id . '/webhooks.json');
        return $this->collectStructure($items->json())
            ->map(function ($webhook) {
                $webhook->shop = $this->shop;
                return $webhook;
            });
    }

    /**
     * Retrieve a single webhook
     *
     * @param string $id
     * @return Webhook
     * @throws ConnectionException
     * @throws RequestException
     */
    public function find(string $id): Webhook
    {
        $item = $this->client->doRequest('shops/' . $this->shop->id . '/webhooks/' . $id . '.json');
        return Webhook::from($item->json());

    }

    /**
     * Create a webhook
     *
     * @param EventsEnum $event - AKA topic
     * @param string $url
     * @return Webhook
     * @throws ConnectionException
     * @throws RequestException
     * @throws Exception
     */
    public function create(EventsEnum $event, string $url): Webhook
    {
        if (!config('printify.webhook_secret', false)) {
            throw new Exception('Webhook secret is not defined');
        }
        $data = [
            'topic' => $event,
            'url' => $url,
            'secret' => config('printify.webhook_secret'),
        ];
        $item = $this->client->doRequest('shops/' . $this->shop->id . '/webhooks.json', 'POST', $data);
        return Webhook::from($item->json());
    }

    /**
     * Modify a webhook URL
     *
     * @param string $id
     * @param string $url
     * @return Webhook
     * @throws ConnectionException
     * @throws RequestException
     */
    public function update(string $id, string $url): Webhook
    {
        $data = [
            'url' => $url
        ];
        $item = $this->client->doRequest('shops/' . $this->shop->id . '/webhooks/' . $id . '.json', 'PUT', $data);
        return Webhook::from($item->json());
    }

    /**
     * Delete a webhook
     *
     * @param string $id
     * @param string|null $host - The host from the webhook URL (required by API)
     * @return boolean
     * @throws ConnectionException
     * @throws RequestException
     */
    public function delete(string $id, ?string $host = null): bool
    {
        $uri = 'shops/' . $this->shop->id . '/webhooks/' . $id . '.json';
        if ($host) {
            $uri .= '?host=' . urlencode($host);
        }
        $response = $this->client->doRequest($uri, 'DELETE');
        return $response->ok();
    }

}

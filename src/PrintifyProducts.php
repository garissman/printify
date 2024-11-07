<?php

namespace Garissman\Printify;

use Exception;
use Garissman\Printify\Structures\Product;
use Garissman\Printify\Structures\Shop;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrintifyProducts extends PrintifyBaseEndpoint
{
    protected string $structure = Product::class;

    public function __construct(PrintifyApiClient $client, public Shop $shop)
    {
        parent::__construct($client);
    }

    /**
     * @throws Exception
     */
    public function all(array $query_options = []): LengthAwarePaginator|Collection
    {
        if (empty($query_options) || !array_key_exists('limit', $query_options)) {
            $query_options['limit'] = 50;
        }
        $uri = 'shops/' . $this->shop->id . '/products.json';
        $items = $this->client->doRequest(uri: $uri, payload: $query_options);
        return $this->collectStructure($items->json(), $query_options);
    }

    /**
     * Retrieve a product
     *
     * @param string $id
     * @return Product
     * @throws ConnectionException
     * @throws RequestException
     */
    public function find(string $id): Product
    {
        $item = $this->client->doRequest('shops/' . $this->shop->id . '/products/' . $id . '.json');
        $item=$item->json();
        $item['shop']=$this->shop;
        return new Product($item);
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        $item = $this->_api_client->doRequest('shops/' . $this->shop_id . '/products.json', 'POST', $data);
        return new Product($item);
    }

    /**
     * Update a product
     * A product can be updated partially or as a whole document. When updating variants, all variants must be present in the request
     *
     * @param int $id
     * @param array $data
     * @return Product
     */
    public function update($id, array $data): Product
    {
        $item = $this->_api_client->doRequest('shops/' . $this->shop_id . '/products/' . $id . '.json', 'PUT', $data);
        return new Product($item);
    }

    /**
     * Delete a product
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id): bool
    {
        $this->_api_client->doRequest('shops/' . $this->shop_id . '/products/' . $id . '.json', 'DELETE');
        return $this->_api_client->status_code === 200;
    }

    /**
     * Publish a product
     * This does not implement any publishing action unless the Printify store is connected to one of our other
     * supported sales channel integrations, if your store is custom and is subscribed to the product::pubish::started event,
     * that event will be triggered and the properties that are set in the request body will be set in the event payload for your store to
     * react to if implemented. The case is the same for attempting to publish a product from the Printify app.
     * See product events (https://developers.printify.com/#product-events) for reference.
     *
     * @param int $product_id
     * @param array $publishable_items - Override to specify the publish
     * @return boolean
     */
    public function publish($product_id, $publishable_items = null): bool
    {
        if (!$publishable_items) {
            $publishable_items = [
                'title' => true,
                'description' => true,
                'images' => true,
                'variants' => true,
                'tags' => true
            ];
        }
        $this->_api_client->doRequest('shops/' . $this->shop_id . '/products/' . $product_id . '/publish.json', 'POST', $publishable_items);
        return $this->_api_client->status_code === 200;
    }

    /**
     * Set product publish status to succeeded
     * Using this endpoint removes the product from the locked status on the Printify app and sets the the it's
     * external property with the handle you provide in the request body.
     *
     * @param string $product_id
     * @param string $handle
     * @return boolean
     * @throws ConnectionException
     * @throws RequestException
     */
    public function publishing_succeeded(string $product_id, string $handle): bool
    {
        $data = [
            'external' => [
                'id' => $product_id,
                'handle' => $handle
            ]
        ];
        return $this->client
            ->doRequest(
                'shops/' . $this->shop->id . '/products/' . $product_id . '/publishing_succeeded.json',
                'POST',
                $data
            )->ok();

    }

    /**
     * Set product publish status to failed
     * Using this endpoint removes the product from the locked status on the Printify app
     *
     * @param int $product_id
     * @param string $reason
     * @return boolean
     */
    public function publishing_failed($product_id, string $reason): bool
    {
        $data = [
            'reason' => $reason
        ];
        $this->_api_client->doRequest('shops/' . $this->shop_id . '/products/' . $product_id . '/publishing_failed.json', 'POST', $data);
        return $this->_api_client->status_code === 200;
    }

    /**
     * Notify that a product has been unpublished
     *
     * @param int $id
     * @return boolean
     */
    public function unpublish($id): bool
    {
        $this->_api_client->doRequest('shops/' . $this->shop_id . '/products/' . $id . '/unpublish.json', 'POST');
        return $this->_api_client->status_code === 200;
    }
}

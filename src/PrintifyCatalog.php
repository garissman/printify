<?php

namespace Garissman\Printify;

use Garissman\Printify\Structures\Catalog\Blueprint;
use Garissman\Printify\Structures\Catalog\PrintProvider;
use Garissman\Printify\Structures\Catalog\Shipping;
use Garissman\Printify\Structures\Catalog\Variant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrintifyCatalog extends PrintifyBaseEndpoint
{
    protected string $structure = Blueprint::class;

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function all(array $query_options = []): LengthAwarePaginator|Collection
    {
        $response = $this->client->doRequest('catalog/blueprints.json');
        return $this->collectStructure($response->json());
    }

    /**
     * Retrieve a specific blueprint
     *
     * @param int $id
     * @return Blueprint
     * @throws ConnectionException
     * @throws RequestException
     */
    public function find($id): Blueprint
    {
        $response = $this->client->doRequest('catalog/blueprints/' . $id . '.json');
        return Blueprint::from($response->json());
    }

    /**
     * Retrieve a list of all print providers that fulfill orders for a specific blueprint
     *
     * @param int $blueprint_id
     * @return Collection
     * @throws ConnectionException
     * @throws RequestException
     */
    public function print_providers($blueprint_id): Collection
    {
        $response = $this->client->doRequest('catalog/blueprints/' . $blueprint_id . '/print_providers.json');
        return $this->collectStructure($response->json(), [], PrintProvider::class);
    }

    /**
     * Retrieve a list of all variants of a blueprint from a specific print provider
     *
     * @param int $blueprint_id
     * @param int $print_provider_id
     * @return Collection
     * @throws ConnectionException
     * @throws RequestException
     */
    public function print_provider_variants($blueprint_id, $print_provider_id): Collection
    {
        $response = $this->client->doRequest('catalog/blueprints/' . $blueprint_id . '/print_providers/' . $print_provider_id . '/variants.json');
        $data = $response->json();
        return $this->collectStructure($data['variants'] ?? [], [], Variant::class);
    }

    /**
     * Retrieve the shipping information for all variants of a blueprint from a specific print provider
     *
     * @param int $blueprint_id
     * @param int $print_provider_id
     * @return Shipping
     * @throws ConnectionException
     * @throws RequestException
     */
    public function print_provider_shipping($blueprint_id, $print_provider_id): Shipping
    {
        $response = $this->client->doRequest('catalog/blueprints/' . $blueprint_id . '/print_providers/' . $print_provider_id . '/shipping.json');
        return Shipping::from($response->json());
    }

    /**
     * Retrieve a list of all available print-providers
     *
     * @return Collection
     * @throws ConnectionException
     * @throws RequestException
     */
    public function all_print_providers(): Collection
    {
        $response = $this->client->doRequest('catalog/print_providers.json');
        return $this->collectStructure($response->json(), [], PrintProvider::class);
    }

    /**
     * Retrieve a specific print-provider and a list of associated blueprint offerings
     *
     * @param int $id
     * @return PrintProvider
     * @throws ConnectionException
     * @throws RequestException
     */
    public function print_provider($id): PrintProvider
    {
        $response = $this->client->doRequest('catalog/print_providers/' . $id . '.json');
        return PrintProvider::from($response->json());
    }
}

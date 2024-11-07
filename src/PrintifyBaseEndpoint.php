<?php

namespace Garissman\Printify;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class PrintifyBaseEndpoint
{
    /**
     * The endpoint structure
     *
     */
    protected string $structure;

    /**
     * Constructor
     *
     * @param PrintifyApiClient $client
     */
    public function __construct(public PrintifyApiClient $client)
    {
    }

    /**
     * Get all items from an endpoint
     *
     * @param array $query_options - URI Query options
     * @return LengthAwarePaginator|Collection - Structured Items in an array
     */
    abstract public function all(array $query_options = []): LengthAwarePaginator|Collection;

    /**
     * Creates a collection of a given endpoint structure
     *
     * @param array $items
     * @param array $payload
     * @return LengthAwarePaginator|Collection
     */
    protected function collectStructure(array $items, array $payload = []): LengthAwarePaginator|Collection
    {
        $structure = $this->structure;
        $collection = new Collection([]);
        if (isset($items['data'])) {
            foreach ($items['data'] as &$item) {
                $item = new $structure($item);
            }
            $collection = new LengthAwarePaginator(
                $items['data'],
                $items['total'],
                $payload['limit'],
                $items['current_page']
            );
        } else {
            foreach ($items as $item) {
                $collection->add(new $structure($item));
            }
        }

        return $collection;
    }
}

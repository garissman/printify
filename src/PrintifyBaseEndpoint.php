<?php

namespace Garissman\Printify;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

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
     * @param string|null $structureOverride
     * @return LengthAwarePaginator|Collection
     */
    protected function collectStructure(array $items, array $payload = [], ?string $structureOverride = null): LengthAwarePaginator|Collection
    {
        $structure = $structureOverride ?? $this->structure;
        $collection = new Collection([]);
        if (isset($items['data'])) {
            foreach ($items['data'] as &$item) {
                $item = $structure::from($item);
            }
            $collection = new LengthAwarePaginator(
                $items['data'],
                $items['total'],
                $payload['limit'] ?? 10,
                $items['current_page']
            );
        } else {
            foreach ($items as $item) {
                $collection->add($structure::from($item));
            }
        }

        return $collection;
    }

    /**
     * Get all items using lazy collection for memory-efficient iteration
     *
     * This method automatically handles pagination and yields items one at a time,
     * making it suitable for processing large datasets without loading everything
     * into memory.
     *
     * @param int $perPage - Items per page (default 100)
     * @return LazyCollection
     */
    public function cursor(int $perPage = 100): LazyCollection
    {
        return LazyCollection::make(function () use ($perPage) {
            $page = 1;
            $hasMore = true;

            while ($hasMore) {
                $results = $this->all(['page' => $page, 'limit' => $perPage]);

                if ($results instanceof LengthAwarePaginator) {
                    foreach ($results->items() as $item) {
                        yield $item;
                    }
                    $hasMore = $results->hasMorePages();
                } else {
                    foreach ($results as $item) {
                        yield $item;
                    }
                    $hasMore = $results->count() === $perPage;
                }

                $page++;
            }
        });
    }
}

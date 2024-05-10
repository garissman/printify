<?php

namespace Printify;

abstract class PrintifyBaseEndpoint
{
    /**
     * Api Client
     *
     * @var \Printify\PrintifyApiClient
     */
    protected $_api_client = null;

    /**
     * The endpoint structure
     *
     * @var \Printify\Structures\*
     */
    protected $_structure = null;

    /**
     * Constructor
     *
     * @param PrintifyApiClient $api_client
     */
    public function __construct(PrintifyApiClient $api_client)
    {
        $this->_api_client = $api_client;
    }

    /**
     * Get all items from a endpoint
     *
     * @param array $query_options - URI Query options
     * @return array - Structured Items in an array
     */
    abstract public function all(array $query_options = []): Collection;

    /**
     * Creates a collection of a given endpoint structure
     *
     * @param array $items
     * @param \Printify\Structures\* $structure - Use a different structure than the structure property
     * @return array
     */
    protected function collectStructure(array $items, $structure = null): Collection
    {
        if (!$structure) {
            $structure = $this->_structure;
        }
        $collection = collect();
        if (isset($items['data'])) {
            $collection->current_page = $items['current_page'];
            $collection->to = $items['to'];
            $collection->from = $items['from'];
            $collection->last_page = $items['last_page'];
            $collection->total = $items['total'];
            $items = $items['data'];
        }
        foreach ($items as $item) {
            $collection->add(new $structure($item));
        }
        return $collection;
    }
}

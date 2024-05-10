<?php

namespace Garissman\Printify\Structures;

abstract class BaseStructure
{
    /**
     * Attributes of the structure
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Construct
     *
     * @param object|array $attributes - Fillable attributes
     */
    public function __construct($attributes = null)
    {
        if (is_array($attributes)) {
            $attributes = (object) $attributes;
        }
        if ($attributes) {
            $this->fill($attributes);
        }
    }

    /**
     * Fills a structure with given values
     *
     * @param object $values
     * @return void
     */
    abstract public function fill(object $values): void;

    /**
     * Returns all the attributes in an array
     * TODO: Fix for nested objects
     *
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->attributes), true);
    }

    /**
     * Structure attributes can be used as objects
     *
     * @param string $key
     * @return void
     */
    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
        return $this->attributes[$key];
    }

    /**
     * Creates a collection of a given structure
     *
     * @param array $items
     * @param \Printify\Structures\* $structure - Use a different structure than the structure property
     * @return array
     */
    protected function collectStructure(array $items, $structure): array
    {
        $collection = [];
        foreach ($items as $item) {
            $collection[] = new $structure($item);
        }
        return $collection;
    }
}

<?php

namespace Garissman\Printify\Structures\Catalog;

use Spatie\LaravelData\Data;

/**
 * Variant Object represents a product variant from the catalog
 */
class Variant extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly mixed $options,
        public readonly array $placeholders,
        public readonly array $decoration_methods = [],
        public readonly ?string $sku = null,
        public readonly ?int $price = null,
        public readonly ?int $cost = null,
        public readonly ?int $grams = null,
        public readonly ?bool $is_enabled = null,
        public readonly ?bool $is_default = null,
        public readonly ?bool $is_available = null,
    ) {}
}

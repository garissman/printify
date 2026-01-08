<?php

namespace Garissman\Printify\Structures\Order;

use Spatie\LaravelData\Data;

/**
 * LineItem Object represents a single product item in an order
 */
class LineItem extends Data
{
    public function __construct(
        public readonly string $product_id,
        public readonly int $quantity,
        public readonly int $variant_id,
        public readonly int $print_provider_id,
        public readonly int $cost,
        public readonly int $shipping_cost,
        public readonly string $status,
        public readonly mixed $metadata,
        public readonly ?string $sent_to_production_at = null,
        public readonly ?string $fulfilled_at = null,
    ) {}
}

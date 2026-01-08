<?php

namespace Garissman\Printify\Structures\Order;

use Spatie\LaravelData\Data;

/**
 * Shipment Object represents tracking information for a shipped order
 */
class Shipment extends Data
{
    public function __construct(
        public readonly string $carrier,
        public readonly string $number,
        public readonly string $url,
        public readonly ?string $delivered_at = null,
    ) {}
}

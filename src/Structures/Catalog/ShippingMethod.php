<?php

namespace Garissman\Printify\Structures\Catalog;

use Spatie\LaravelData\Data;

/**
 * ShippingMethod Object represents a specific shipping method from V2 API
 */
class ShippingMethod extends Data
{
    public function __construct(
        public readonly string $method,
        public readonly mixed $handling_time,
        public readonly array $profiles,
    ) {}
}

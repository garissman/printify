<?php

namespace Garissman\Printify\Structures\Product;

use Spatie\LaravelData\Data;

/**
 * GPSR (General Product Safety Regulation) Object represents product safety information
 *
 * Required for products sold in the EU market
 */
class GPSR extends Data
{
    public function __construct(
        public readonly ?string $legal_disclaimer = null,
        public readonly ?array $manufacturer = null,
        public readonly ?array $responsible_person = null,
    ) {}
}

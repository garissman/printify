<?php

namespace Garissman\Printify\Structures\Catalog;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * PrintProvider Object represents a fulfillment provider in Printify catalog
 */
class PrintProvider extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly mixed $location = null,
        #[DataCollectionOf(Blueprint::class)]
        public readonly ?DataCollection $blueprints = null,
    ) {}
}

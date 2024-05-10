<?php

namespace Garissman\Printify\Structures\Order;

use Garissman\Printify\Structures\BaseStructure;

class Shipment extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'carrier' => $attribute->carrier,
            'number' => $attribute->number,
            'url' => $attribute->url,
            'delivered_at' => isset($attribute->delivered_at) ? $attribute->delivered_at : null
        ];
    }
}

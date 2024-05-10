<?php

namespace Garissman\Printify\Structures\Order;

use Garissman\Printify\Structures\BaseStructure;

class LineItem extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'product_id' => $attribute->product_id,
            'quantity' => (int) $attribute->quantity,
            'variant_id' => (int) $attribute->variant_id,
            'print_provider_id' => (int) $attribute->print_provider_id,
            'cost' => (int) $attribute->cost,
            'shipping_cost' => (int) $attribute->shipping_cost,
            'status' => $attribute->status,
            'metadata' => $attribute->metadata,
            'sent_to_production_at' => isset($attribute->sent_to_production_at) ? $attribute->sent_to_production_at : null,
            'fulfilled_at' => isset($attribute->fulfilled_at) ? $attribute->fulfilled_at : null
        ];
    }

}

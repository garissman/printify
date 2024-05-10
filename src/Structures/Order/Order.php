<?php

namespace Garissman\Printify\Structures\Order;

use Garissman\Printify\Structures\BaseStructure;

class Order extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'id' => $attribute->id,
            'address_to' => $attribute->address_to,
            'line_items' => $this->collectStructure($attribute->line_items, LineItem::class),
            'metadata' => $attribute->metadata,
            'total_price' => (int) $attribute->total_price,
            'total_shipping' => (int) $attribute->total_shipping,
            'total_tax' => (int) $attribute->total_tax,
            'status' => $attribute->status,
            'shipping_method' => (int) $attribute->shipping_method, //"1" is for standard shipping and "2" is for express shipping
            'shipments' => isset($attribute->shipments) ? $this->collectStructure($attribute->shipments, Shipment::class) : null,
            'created_at' => $attribute->created_at,
            'sent_to_production_at' => isset($attribute->sent_to_production_at) ? $attribute->sent_to_production_at : null,
            'fulfilled_at' => isset($attribute->fulfilled_at) ? $attribute->fulfilled_at : null
        ];
    }
}

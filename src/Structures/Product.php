<?php

namespace Garissman\Printify\Structures;

class Product extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'id' => $attribute->id,
            'title' => $attribute->title,
            'description' => $attribute->description,
            'tags' => $attribute->tags,
            'options' => $attribute->options,
            'variants' => $attribute->variants,
            'images' => $attribute->images,
            'created_at' => $attribute->created_at,
            'updated_at' => $attribute->updated_at,
            'visible' => $attribute->visible === 'true' ? true : false,
            'is_locked' => $attribute->is_locked === 'true' ? true : false,
            'blueprint_id' => (int) $attribute->blueprint_id,
            'user_id' => (int) $attribute->user_id,
            'shop_id' => (int) $attribute->shop_id,
            'print_provider_id' => (int) $attribute->print_provider_id,
            'print_areas' => $attribute->print_areas,
            'print_details' => isset($attribute->print_details) ? $attribute->print_details : null,
            'external' => isset($attribute->external) ? $attribute->external : null,
            'is_locked' => isset($attribute->is_locked) ? $attribute->is_locked : null,
            'sales_channel_properties' => $attribute->sales_channel_properties
        ];
    }
}

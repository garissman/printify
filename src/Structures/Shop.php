<?php

namespace Garissman\Printify\Structures;

class Shop extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'id' => $attribute->id,
            'title' => $attribute->title,
            'sales_channel' => $attribute->sales_channel
        ];
    }
}

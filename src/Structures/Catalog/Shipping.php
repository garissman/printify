<?php

namespace Garissman\Printify\Structures\Catalog;

use Garissman\Printify\Structures\BaseStructure;

class Shipping extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'handling_time' => $attribute->handling_time,
            'profiles' => $attribute->profiles
        ];
    }
}

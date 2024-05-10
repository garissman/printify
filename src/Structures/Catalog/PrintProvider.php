<?php

namespace Garissman\Printify\Structures\Catalog;

use Garissman\Printify\Structures\BaseStructure;

class PrintProvider extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'id' => (int) $attribute->id,
            'title' => $attribute->title,
            'location' => isset($attribute->location) ? $attribute->location : null
        ];
        if (isset($attribute->blueprints)) {
            $this->attributes['blueprints'] = $this->collectStructure($attribute->blueprints, Blueprint::class);
        }
    }
}

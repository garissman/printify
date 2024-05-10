<?php

namespace Garissman\Printify\Structures;

class Image extends BaseStructure
{
    public function fill(object $attribute): void
    {
        $this->attributes = [
            'id' => $attribute->id,
            'file_name' => $attribute->file_name,
            'height' => (int) $attribute->height,
            'width' => (int) $attribute->width,
            'size' => (int) $attribute->size,
            'mime_type' => $attribute->mime_type,
            'preview_url' => $attribute->preview_url,
            'upload_time' => $attribute->upload_time
        ];
    }
}

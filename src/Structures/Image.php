<?php

namespace Garissman\Printify\Structures;

class Image extends BaseStructure
{
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';

}

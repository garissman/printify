<?php

namespace Garissman\Printify\Structures;

class Product extends BaseStructure
{
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}

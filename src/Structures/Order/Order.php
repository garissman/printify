<?php

namespace Garissman\Printify\Structures\Order;

use Garissman\Printify\Structures\BaseStructure;

class Order extends BaseStructure
{
    public $incrementing = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
}

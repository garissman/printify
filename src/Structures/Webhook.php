<?php

namespace Garissman\Printify\Structures;

use Garissman\Printify\Facades\Printify;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

/**
 * @property Shop shop
 */
class Webhook extends BaseStructure
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function delete(): bool
    {
        return $this->shop->webhook()->delete($this->id);
    }
    public function shop(): Shop
    {
        return Printify::shop()
            ->all()
            ->filter(function ($shop) {return $shop->id === $this->shop_id;})
            ->first();
    }
}

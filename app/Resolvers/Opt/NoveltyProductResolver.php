<?php

namespace App\Resolvers\Opt;

class NoveltyProductResolver extends BaseProductResolver
{
    public function resolve()
    {
        return $this
            ->getBaseProductQuery(
                $this->retrieveCurrency(null),
                $this->getWholesaleStoreIds()
            )
            ->latest('created_at')
            ->limit(10);
    }
}

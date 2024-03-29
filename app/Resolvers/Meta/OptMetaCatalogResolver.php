<?php

namespace App\Resolvers\Meta;

class OptMetaCatalogResolver
{
    public function resolver(array $params)
    {
        return [
            'heading' => 'Каталог',
            'description' => 'Iron-Addicts.KZ оптовые товары',
            'keywords' => ['каталог'],
            'title' => 'Opt.Iron-Addicts.KZ | оптовые товары'
        ];
    }
}

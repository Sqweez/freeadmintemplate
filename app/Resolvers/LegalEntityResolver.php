<?php

namespace App\Resolvers;

use App\Concerns\UseQuickBindings;
use App\LegalEntity;

class LegalEntityResolver
{
    use UseQuickBindings;

    private string $name;
    private string $iin;
    private string $address;

    public function __construct($entityId)
    {
        if (!$entityId) {
            $this->_setDefaults();
        } else {
            $entity = LegalEntity::query()
                ->whereKey($entityId)
                ->first();
            if (!$entity) {
                $this->_setDefaults();
            } else {
                $this->name = $entity->name;
                $this->address = $entity->address;
                $this->iin = $entity->iin;
            }
        }
    }

    private function _setDefaults () {
        $this->name = config('legal.default_legal_name');
        $this->iin = config('legal.default_legal_iin');
        $this->address = config('legal.default_legal_address');
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getIIN(): string
    {
        return $this->iin;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

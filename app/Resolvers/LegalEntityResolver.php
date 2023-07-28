<?php

namespace App\Resolvers;

use App\Concerns\UseQuickBindings;
use App\LegalEntity;
use App\Models\v2\BankAccount;

class LegalEntityResolver
{
    use UseQuickBindings;

    private string $name;
    private string $iin;
    private string $address;
    private string $boss;
    private $bankAccount;

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
                $this->boss = $entity->boss;
            }
        }
    }

    private function _setDefaults () {
        $this->name = config('legal.default_legal_name');
        $this->iin = config('legal.default_legal_iin');
        $this->address = config('legal.default_legal_address');
        $this->boss = config('legal.default_legal_name');
    }

    public function setBankAccount($bankAccountId): LegalEntityResolver
    {
        $this->bankAccount = BankAccount::find($bankAccountId);
        return $this;
    }

    public function getBankAccount(): BankAccount
    {
        return $this->bankAccount;
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

    public function getBoss(): string
    {
        return $this->boss;
    }
}

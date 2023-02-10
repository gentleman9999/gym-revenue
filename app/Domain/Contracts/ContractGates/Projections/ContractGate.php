<?php

declare(strict_types=1);

namespace App\Domain\Contracts\ContractGates\Projections;

use App\Enums\ContractGateTypeEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractGate extends GymRevProjection
{
    use HasFactory;

    /** @var array<string> */
    protected $fillable = ['client_id', 'contract_id', 'entity_id', 'entity_type'];

    /** @var array<string, string> */
    protected $casts = [
        'contract_gate_type' => ContractGateTypeEnum::class,
    ];

    public static function validateContract(string $contractId, array $entityIds): bool
    {
        if (
            self::whereContractId($contractId)->exists() && ! self::whereContractId($contractId)
                ->whereIn('entity_id', $entityIds)
                ->exists()
        ) {
            return false;
        }

        return true;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}

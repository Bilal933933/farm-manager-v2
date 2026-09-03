<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentTerms;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'land_id',
        'contract_type',
        'counterparty_party_id',
        'owner_party_id',
        'start_date',
        'end_date',
        'financial_value',
        'revenue_share_percentage',
        'payment_terms',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'contract_type' => ContractType::class,
            'status' => ContractStatus::class,
            'payment_terms' => PaymentTerms::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'financial_value' => 'decimal:2',
            'revenue_share_percentage' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Land, $this>
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }
}

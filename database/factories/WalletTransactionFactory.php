<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => WalletTransaction::TYPE_GRANT,
            'amount' => 1000,
            'balance_after' => 1000,
            'ref_type' => WalletTransaction::REF_TYPE_SYSTEM,
            'ref_id' => 'factory-seed',
            'unique_key' => 'wallet-factory-'.Str::lower(Str::random(12)),
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}


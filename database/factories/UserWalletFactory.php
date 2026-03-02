<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserWallet>
 */
class UserWalletFactory extends Factory
{
    protected $model = UserWallet::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => 1000,
        ];
    }
}


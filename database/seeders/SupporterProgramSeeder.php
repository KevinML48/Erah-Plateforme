<?php

namespace Database\Seeders;

use App\Services\SupporterAccessResolver;
use Illuminate\Database\Seeder;

class SupporterProgramSeeder extends Seeder
{
    public function run(): void
    {
        $resolver = app(SupporterAccessResolver::class);
        $resolver->ensureConfiguredPlans();
        $resolver->ensureCommunityGoals();
    }
}

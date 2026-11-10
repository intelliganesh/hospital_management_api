<?php

namespace App\Console\Commands;

use App\Services\Rbac\RbacService;
use Illuminate\Console\Command;

class RbacSeed extends Command
{
    protected $signature = 'rbac:seed';
    protected $description = 'Create RBAC permissions and grant the default role matrix (additive, idempotent)';

    public function handle(RbacService $rbac): int
    {
        $this->info(json_encode($rbac->seed()));
        return 0;
    }
}

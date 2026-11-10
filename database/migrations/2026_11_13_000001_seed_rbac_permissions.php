<?php

use App\Services\Rbac\RbacService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/** Additive + idempotent: creates the permission catalogue and grants the frontend matrix. Never revokes. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['permissions', 'roles', 'role_has_permissions'] as $t) {
            if (! Schema::hasTable($t)) {
                return;
            }
        }
        app(RbacService::class)->seed();
    }

    public function down(): void
    {
        // intentionally empty: set RBAC_MODE=off to disable; keep admin-customised grants.
    }
};

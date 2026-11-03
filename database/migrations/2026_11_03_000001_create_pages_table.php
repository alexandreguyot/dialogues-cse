<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = ['page_access', 'page_create', 'page_edit', 'page_show', 'page_delete'];

    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('theme')->default('light');
            $table->string('meta_description')->nullable();
            $table->boolean('in_menu')->default(false);
            $table->boolean('is_published')->default(false);
            $table->json('blocks');
            $table->timestamps();
        });

        // Base déjà en service : le seeder ne repassera pas, on ajoute les droits ici.
        // Sur une base vide, PermissionsTableSeeder s'en charge avec ses ids fixes.
        if (! DB::table('permissions')->exists()) {
            return;
        }

        foreach (self::PERMISSIONS as $title) {
            $permissionId = DB::table('permissions')->where('title', $title)->value('id')
                ?? DB::table('permissions')->insertGetId(['title' => $title]);

            foreach (DB::table('roles')->whereIn('id', [1, 2])->pluck('id') as $roleId) {
                DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');

        $ids = DB::table('permissions')->whereIn('title', self::PERMISSIONS)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};

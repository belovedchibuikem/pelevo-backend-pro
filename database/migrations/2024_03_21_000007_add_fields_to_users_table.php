<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable();
            $table->string('device_id')->nullable()->unique();
            $table->string('device_name')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('profile_image_url')->nullable()->after('last_login_at');
            $table->json('subscribed_categories')->nullable()->after('profile_image_url');
            $table->float('balance')->nullable()->after('subscribed_categories');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'device_id',
                'device_name',
                'country',
                'phone',
                'is_admin',
                'is_blocked',
                'last_login_at',
                'last_login_ip',
                'profile_image_url',
                'subscribed_categories',
                'balance',
            ]);
        });
    }
}; 
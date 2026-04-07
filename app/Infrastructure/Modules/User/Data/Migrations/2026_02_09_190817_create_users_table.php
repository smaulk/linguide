<?php

use App\Core\Modules\User\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->enum('status', UserStatus::values())
                ->default(UserStatus::INACTIVE->value);

            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

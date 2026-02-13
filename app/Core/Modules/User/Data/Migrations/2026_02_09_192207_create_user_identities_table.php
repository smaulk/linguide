<?php

use App\Core\Modules\User\Enums\UserProviderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->enum('provider', UserProviderType::values());
            $table->string('provider_user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('email_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['provider', 'provider_user_id']);
            $table->unique('email');
        });

        $this->upProviderConstraints();
    }

    public function down(): void
    {
        $this->downProviderConstraints();
        Schema::dropIfExists('user_identities');
    }

    private function upProviderConstraints(): void
    {
        $emailProvider = UserProviderType::EMAIL->value;

        DB::statement("
            ALTER TABLE user_identities
            ADD CONSTRAINT chk_user_identities_auth_shape
            CHECK (
                (
                    provider = '{$emailProvider}'
                    AND email IS NOT NULL
                    AND password IS NOT NULL
                    AND provider_user_id IS NULL
                )
                OR
                (
                    provider <> '{$emailProvider}'
                    AND provider_user_id IS NOT NULL
                    AND email IS NULL
                    AND password IS NULL
                )
            )
        ");
    }

    private function downProviderConstraints(): void
    {
        DB::statement("
            ALTER TABLE user_identities DROP CONSTRAINT IF EXISTS chk_user_identities_auth_shape
        ");
    }
};

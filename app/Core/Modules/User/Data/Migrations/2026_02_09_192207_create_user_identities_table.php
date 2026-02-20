<?php

use App\Core\Modules\User\Enums\UserProviderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->index('user_id');

            $table->enum('provider', UserProviderType::values());
            $table->string('provider_user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestampTz('email_verified_at')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        $this->createProviderUserIdUnique();
        $this->createEmailUnique();
        $this->upProviderConstraints();
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS ux_user_identities_provider_pid');
        DB::statement('DROP INDEX IF EXISTS ux_user_identities_email');
        DB::statement("ALTER TABLE user_identities 
            DROP CONSTRAINT IF EXISTS chk_user_identities_auth_shape");

        Schema::dropIfExists('user_identities');
    }

    /**
     * Добавляет уникальный частичный индекс для provider и provider_user_id,
     * где provider_user_id не NULL
     */
    private function createProviderUserIdUnique(): void
    {
        DB::statement("
            CREATE UNIQUE INDEX ux_user_identities_provider_pid
            ON user_identities (provider, provider_user_id)
            WHERE provider_user_id IS NOT NULL
        ");
    }

    /**
     * Добавляет уникальный частичный индекс для email,
     * где email не NULL
     */
    private function createEmailUnique(): void
    {
        DB::statement("
            CREATE UNIQUE INDEX ux_user_identities_email
            ON user_identities (email) 
            WHERE email IS NOT NULL
        ");
    }

    /**
     * Устанавливает ограничение для атрибутов конкретного провайдера
     */
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
};

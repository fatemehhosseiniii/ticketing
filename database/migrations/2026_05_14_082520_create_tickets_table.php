<?php

use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code')->unique()->index();
            $table->string('subject',120);
            $table->text('description');
            $table->string('file_src')->nullable();

            $table->tinyInteger('status')->default(TicketStatus::New)->index();
            $table->text('status_message')->nullable();

            $table->foreignId('creator_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('expert_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

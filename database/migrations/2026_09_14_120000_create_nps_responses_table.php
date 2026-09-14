<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nps_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('score')->nullable(); // 0..10 tras responder
            $table->text('comment')->nullable();
            $table->string('token', 40)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'token']);
            $table->index('responded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nps_responses');
    }
};

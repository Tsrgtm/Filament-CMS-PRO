<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('url', 1000);
            $table->string('secret', 255)->nullable();
            $table->json('events');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_webhooks');
    }
};

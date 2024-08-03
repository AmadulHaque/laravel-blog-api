<?php

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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('short_description', 450)->nullable();
            $table->longText('content');
            $table->string('tags')->nullable();
            $table->enum('allow_comments',[1,0])->default(0);
            $table->enum('is_featured',[1,0])->default(0);
            $table->enum('status', [1, 2, 3])->default(1)->comment('1=Published 2=Pending 3=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

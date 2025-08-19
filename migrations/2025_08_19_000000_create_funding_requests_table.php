<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('funding_requests', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('user_id');
    $table->string('tx_hash', 100)->unique();
    $table->decimal('amount', 32, 18);
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->text('reason')->nullable();
    $table->timestamps();

    $table->index('user_id');
    $table->index('tx_hash');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

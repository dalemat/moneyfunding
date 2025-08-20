<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('erc20_transactions')) {
            return;
        }

        $schema->create('erc20_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('tx_hash', 66)->unique();
            $table->decimal('amount', 36, 18);
            $table->decimal('points', 15, 2);
            $table->enum('status', ['pending', 'confirming', 'confirmed', 'failed'])->default('pending');
            $table->string('contract_address', 42);
            $table->unsignedBigInteger('block_number')->nullable();
            $table->unsignedInteger('confirmation_count')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('erc20_transactions');
    }
];

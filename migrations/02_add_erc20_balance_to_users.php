<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasColumn('users', 'erc20_balance')) {
            return;
        }

        $schema->table('users', function (Blueprint $table) {
            $table->decimal('erc20_balance', 15, 2)->default(0)->after('email');
            $table->index('erc20_balance');
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('erc20_balance');
        });
    }
];

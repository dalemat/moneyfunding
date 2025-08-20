<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return [
    'up' => function () {
        if (!Schema::hasColumn('users', 'money')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('money')->default(0);
            });
        }
    },
    'down' => function () {
        if (Schema::hasColumn('users', 'money')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('money');
            });
        }
    },
];

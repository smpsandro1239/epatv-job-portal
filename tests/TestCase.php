<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use Illuminate\Foundation\Testing\CreatesApplication, DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure SQLite database is fresh
        if (config('database.default') === 'sqlite') {
            $databasePath = config('database.connections.sqlite.database');
            if (file_exists($databasePath)) {
                unlink($databasePath);
            }
            touch($databasePath);
        }
    }
}

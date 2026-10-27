<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckDatabaseConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_confirms_a_working_connection(): void
    {
        $this->artisan('tindziva:check-database')->assertSuccessful();
    }

    public function test_it_fails_with_a_message_when_the_database_is_unreachable(): void
    {
        config(['database.connections.sqlite.database' => '/caminho/inexistente/base.sqlite']);
        app('db')->purge('sqlite');

        $this->artisan('tindziva:check-database')->assertFailed();
    }
}

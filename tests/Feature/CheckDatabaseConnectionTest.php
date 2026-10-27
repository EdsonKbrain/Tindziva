<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckDatabaseConnectionTest extends TestCase
{
    public function test_it_confirms_a_working_connection(): void
    {
        $this->artisan('tindziva:check-database')->assertSuccessful();
    }

    public function test_it_fails_with_a_message_when_the_database_is_unreachable(): void
    {
        config([
            'database.connections.unreachable' => ['driver' => 'sqlite', 'database' => '/caminho/inexistente/base.sqlite', 'prefix' => ''],
            'database.default' => 'unreachable',
        ]);

        $this->artisan('tindziva:check-database')->assertFailed();
    }
}

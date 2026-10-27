<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('tindziva:check-database')]
#[Description('Check the database connection with a simple query (works on restricted shared hosting)')]
class CheckDatabaseConnection extends Command
{
    public function handle(): int
    {
        try {
            DB::connection()->select('select 1');
        } catch (Throwable $exception) {
            $this->components->error('Sem ligação à base de dados: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info(sprintf(
            'Ligação à base de dados «%s» (%s) estabelecida.',
            DB::connection()->getDatabaseName(),
            DB::connection()->getDriverName(),
        ));

        return self::SUCCESS;
    }
}

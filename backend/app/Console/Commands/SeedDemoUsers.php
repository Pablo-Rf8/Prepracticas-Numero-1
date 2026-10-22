<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedDemoUsers extends Command
{
    use ConfirmableTrait;

    protected $signature = 'users:seed-demo
                            {--count=100000 : Cantidad total de usuarios de práctica, entre 1 y 100000}
                            {--force : Permitir la ejecución en producción sin confirmación interactiva}';

    protected $description = 'Completa los usuarios de práctica sin modificar las cuentas existentes';

    public function handle(): int
    {
        $requestedCount = filter_var($this->option('count'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 100000],
        ]);

        if ($requestedCount === false) {
            $this->components->error('La cantidad debe ser un número entero entre 1 y 100000.');

            return self::INVALID;
        }

        if (! $this->confirmToProceed('Se agregarán usuarios de práctica en la base de datos de producción.')) {
            return self::FAILURE;
        }

        $factory = User::factory()->state([
            'password' => Hash::make(Str::random(64)),
            'remember_token' => null,
        ]);
        $timestamp = now()->toDateTimeString();
        $createdCount = 0;

        for ($batchStart = 1; $batchStart <= $requestedCount; $batchStart += 1000) {
            $batchEnd = min($batchStart + 999, $requestedCount);
            $emails = array_map($this->demoEmail(...), range($batchStart, $batchEnd));
            $existingEmails = DB::table('users')->whereIn('email', $emails)->pluck('email')->flip();
            $users = [];

            foreach ($emails as $email) {
                if ($existingEmails->has($email)) {
                    continue;
                }

                $users[] = [
                    ...$factory->raw(['email' => $email]),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            if ($users !== []) {
                $createdCount += DB::table('users')->insertOrIgnore($users);
            }
        }

        $this->components->info("Objetivo completado: {$requestedCount} usuarios de práctica. Creados: {$createdCount}.");

        return self::SUCCESS;
    }

    private function demoEmail(int $number): string
    {
        return sprintf('practica-%06d@usuarios.example.invalid', $number);
    }
}

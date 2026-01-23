<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $config = DB::table('backup_settings')->first();

            if (!$config || !$config->activo) {
                return;
            }

            // Comparar hora configurada
            $now = now()->format('H:i');
            if ($now !== substr($config->hora, 0, 5)) {
                return;
            }

            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');

            $fecha = now()->format('Y-m-d_H-i-s');
            $file = storage_path("app/backups/auto_backup_{$dbName}_{$fecha}.sql");

            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0755, true);
            }

            $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

            $cmd = "\"{$mysqldump}\" -h {$dbHost} -u {$dbUser}";
            if (!empty($dbPass)) {
                $cmd .= " -p{$dbPass}";
            }

            $cmd .= " --databases {$dbName} --single-transaction > \"{$file}\"";

            exec($cmd);

            DB::table('backup_settings')->update([
                'ultimo_respaldo' => now()
            ]);

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

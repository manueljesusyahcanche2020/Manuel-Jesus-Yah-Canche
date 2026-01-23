<?php

namespace App\Http\Controllers\administrador;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;


class BackupController extends Controller
{
    /**
     * Muestra la vista del respaldo
     */
    public function index()
    {
        return view('dashboard.administrador.respaldo');
    }

    /**
     * Genera el respaldo de la base de datos
     */
    public function create()
    {
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        $fecha = now()->format('Y-m-d_H-i-s');
        $fileName = "backup_{$dbName}_{$fecha}.sql";
        $backupPath = storage_path("app/{$fileName}");

        $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

        $command = "\"$mysqldump\" -h {$dbHost} -u {$dbUser}";
        if ($dbPass) {
            $command .= " -p{$dbPass}";
        }

        // 🔥 Dump completo con BD incluida
        $command .= " --databases {$dbName} --single-transaction --routines --triggers --add-drop-database > \"{$backupPath}\"";

        exec($command . " 2>&1", $output, $result);

        if ($result !== 0 || !file_exists($backupPath)) {
            return back()->with('error', implode("\n", $output));
        }

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }

    /**
     * Restaura la base de datos
     */

   public function restore(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            // Cambiamos mimes por extension para evitar errores de deteccion
            'sql_file' => 'required|file'
        ]);

        $file = $request->file('sql_file');
        $fullPath = $file->getRealPath();

        // Configuración de conexión
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Ruta al ejecutable de mysql (Asegúrate que sea la correcta en tu XAMPP)
        $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';

        // Construcción del comando
        // Usamos --force para que continúe si hay errores menores
        $command = "\"$mysqlPath\" -h {$dbHost} -u {$dbUser}";
        
        if ($dbPass) {
            $command .= " -p{$dbPass}";
        }

        $command .= " {$dbName} < \"{$fullPath}\"";

        // Ejecutar el comando
        exec($command . " 2>&1", $output, $result);

        if ($result !== 0) {
            return back()->with('error', 'Error al restaurar: ' . implode("\n", $output));
        }

        return back()->with('success', '¡Base de datos restaurada con éxito!');
    }
    public function config(Request $request)
    {
    $request->validate([
        'frecuencia' => 'required|in:daily,weekly,monthly',
        'hora' => 'required'
    ]);

    DB::table('backup_settings')->updateOrInsert(
        ['id' => 1],
        [
            'frecuencia' => $request->frecuencia,
            'hora' => $request->hora,
            'activo' => 1,
            'updated_at' => now()
        ]
    );

    // Calcular próximo respaldo
    $next = now()->setTimeFromTimeString($request->hora);

    if ($request->frecuencia === 'daily') {
        $next->addDay();
    } elseif ($request->frecuencia === 'weekly') {
        $next->addWeek();
    } else {
        $next->addMonth();
    }

    return back()->with(
        'next_backup',
        $next->format('d/m/Y H:i')
    );
    }
}

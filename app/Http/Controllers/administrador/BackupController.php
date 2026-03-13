<?php

namespace App\Http\Controllers\administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        return view('dashboard.administrador.respaldo');
    }

    /**
     * RESPALDAR TABLAS + DATOS
     */
    public function create()
    {
        $dbName = env('DB_DATABASE');
        $fecha = now()->format('Y-m-d_H-i-s');
        $fileName = "backup_tables_{$dbName}_{$fecha}.sql";

        $sql = "-- Backup de tablas\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = DB::select('SHOW TABLES');

        $key = "Tables_in_{$dbName}";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;

            // Estructura
            $create = DB::select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $create . ";\n\n";

            // Datos
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return is_null($value)
                        ? 'NULL'
                        : "'" . addslashes($value) . "'";
                }, (array)$row);

                $sql .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        Storage::disk('local')->put($fileName, $sql);

        return response()->download(
            storage_path("app/{$fileName}")
        )->deleteFileAfterSend(true);
    }

    /**
     * RESTAURAR TABLAS + DATOS
     */
    public function restore(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'sql_file' => 'required|file'
        ]);

        $sql = file_get_contents($request->file('sql_file')->getRealPath());

        DB::unprepared($sql);

        return back()->with('success', '✅ Base de datos restaurada correctamente');
    }
}

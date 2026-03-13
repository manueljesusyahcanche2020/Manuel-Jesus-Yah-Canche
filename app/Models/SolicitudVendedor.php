<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudVendedor extends Model
{
    protected $table = 'solicitudes_ingre_vendedor';

    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'id_user',
        'nombre_del_solicitante',
        'estado'
    ];
}
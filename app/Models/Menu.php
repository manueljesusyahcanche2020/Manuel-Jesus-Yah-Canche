<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['nombre', 'ruta', 'icono', 'orden'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu_permissions')
                    ->withTimestamps();
    }

}

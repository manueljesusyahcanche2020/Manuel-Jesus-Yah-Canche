<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function menus1()
    {
        return $this->belongsToMany(
            Menu::class,
            'role_menu_permissions'
        );
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu_permissions')
                    ->wherePivot('permitido', 1);
    }

}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->role->nombre !== $role) {

            $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso denegado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .container {
            text-align: center;
            animation: fadeIn 1.2s ease-in-out;
        }

        .lock {
            font-size: 90px;
            margin-bottom: 20px;
            animation: shake 1.5s infinite;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        p {
            opacity: 0.8;
            font-size: 16px;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            50% { transform: translateX(6px); }
            75% { transform: translateX(-6px); }
            100% { transform: translateX(0); }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lock">🔒</div>
        <h1>Acceso denegado</h1>
        <p>No tienes permisos para acceder a esta sección</p>
    </div>
</body>
</html>
HTML;

            return new Response($html, 403, [
                'Content-Type' => 'text/html'
            ]);
        }

        return $next($request);
    }
}

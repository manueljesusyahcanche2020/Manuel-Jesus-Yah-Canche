<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Registro - Peto</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Usando tu imagen de fondo de Peto */
            background: url('https://meridamoderna.com/wp-content/uploads/2022/08/Iglesia-de-peto.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Contenedor principal con Glassmorphism */
        .container {
            position: relative;
            width: 800px;
            max-width: 95%;
            height: 520px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .forms-container {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
        }

        form {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            box-sizing: border-box;
            color: #fff;
        }

        form h2 { text-align: center; margin-bottom: 20px; }

        label {
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            outline: none;
        }

        input::placeholder { color: rgba(255, 255, 255, 0.6); }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover { background-color: rgba(255, 255, 255, 0.5); }

        /* Panel de Animación (Overlay) */
/* Panel de Animación (Overlay) */
        .overlay-panel {
            position: absolute;
            top: 0;
            left: 50%; /* Inicia en la derecha */
            width: 50%;
            height: 100%;
            background: #1e1e2f; /* color sólido, mismo fondo que el body */
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 10;
            transition: all 0.7s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            cursor: pointer;
        }

        .container.active-register .overlay-panel {
            left: 0;
            border-left: none;
            border-right: 2px solid rgba(255, 255, 255, 0.2);
        }

        .overlay-panel h2 { color: #ffcc00; margin-bottom: 10px; }
        .overlay-panel p { color: #fff; text-decoration: underline; font-weight: bold; }

        /* Estilo de Errores de Laravel */
        .error-list {
            background-color: rgba(255, 0, 0, 0.3);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
            list-style: none;
        }
        .error-list li { color: #fff; }

    </style>
</head>
<body>

<div class="container" id="container">
    
    <div class="forms-container">
        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            <h2>Iniciar sesión</h2>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <label>Email:</label>
            <input type="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Ingresar</button>
        </form>

        <form id="registerForm" method="POST" action="{{ route('register') }}">
            @csrf
            <h2>Registrarse</h2>

            <label>Nombre:</label>
            <input type="text" name="name" placeholder="Nombre completo" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Correo electrónico" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Contraseña" required>

            <label>Confirmar contraseña:</label>
            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>

            <button type="submit">Registrarse</button>
        </form>
    </div>

    <div class="overlay-panel" id="toggle-panel">
        <h2 id="overlayTitle">¿No tienes cuenta?</h2>
        <p id="overlayText">Regístrate aquí</p>
    </div>

</div>

<script>
    const container = document.getElementById('container');
    const togglePanel = document.getElementById('toggle-panel');
    const overlayTitle = document.getElementById('overlayTitle');
    const overlayText = document.getElementById('overlayText');

    togglePanel.addEventListener('click', () => {
        container.classList.toggle('active-register');

        if (container.classList.contains('active-register')) {
            overlayTitle.textContent = "¿Ya tienes cuenta?";
            overlayText.textContent = "Inicia sesión";
        } else {
            overlayTitle.textContent = "¿No tienes cuenta?";
            overlayText.textContent = "Regístrate aquí";
        }
    });
</script>

</body>
</html>
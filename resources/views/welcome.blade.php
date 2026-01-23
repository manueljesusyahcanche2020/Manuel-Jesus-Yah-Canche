<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Registro</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            background: url('https://meridamoderna.com/wp-content/uploads/2022/08/Iglesia-de-peto.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 40px 30px;
            width: 300px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            color: #fff;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-container label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .login-container input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-container button:hover {
            background-color: rgba(255, 255, 255, 0.5);
        }

        .toggle-link {
            text-align: center;
            margin-top: 15px;
            cursor: pointer;
            text-decoration: underline;
        }

        .error-list {
            background-color: rgba(255, 0, 0, 0.2);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .error-list li {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 id="form-title">Iniciar sesión</h2>

        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form id="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <label>Email:</label>
            <input type="email" name="email" placeholder="Correo electrónico" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Ingresar</button>
        </form>

        <!-- Registro Form -->
        <form id="register-form" method="POST" action="{{ route('register') }}" style="display:none;">
            @csrf
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


        <div class="toggle-link" id="toggle-link">¿No tienes cuenta? Regístrate</div>
    </div>

    <script>
        const toggleLink = document.getElementById('toggle-link');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const formTitle = document.getElementById('form-title');

        toggleLink.addEventListener('click', () => {
            if(loginForm.style.display === "none") {
                // Mostrar login
                loginForm.style.display = "block";
                registerForm.style.display = "none";
                formTitle.textContent = "Iniciar sesión";
                toggleLink.textContent = "¿No tienes cuenta? Regístrate";
            } else {
                // Mostrar registro
                loginForm.style.display = "none";
                registerForm.style.display = "block";
                formTitle.textContent = "Registrarse";
                toggleLink.textContent = "¿Ya tienes cuenta? Iniciar sesión";
            }
        });
    </script>
</body>
</html>

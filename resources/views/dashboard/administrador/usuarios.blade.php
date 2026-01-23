@extends('dashboard.welcome')

@section('contenido')
<div class="container mt-4">

    <h4 class="mb-4">👤 Usuarios registrados</h4>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Imagen</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>

                    {{-- 👇 Rol (valor directo desde la tabla roles) --}}
                    <td>
                        {{ $user->role->nombre ?? 'Sin rol' }}
                    </td>

                    <td>
                        <img src="{{ $user->imagen 
                            ? asset('storage/'.$user->imagen) 
                            : 'https://cdn-icons-png.flaticon.com/512/149/149071.png' }}"
                            width="40" class="rounded-circle">

                    </td>

                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        No hay usuarios registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection

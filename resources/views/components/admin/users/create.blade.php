<?php

use App\Models\App;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'usuario';

    public array $selectedApps = [];


    public function save(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                'in:admin,usuario',
            ],

            'selectedApps' => [
                'array',
            ],

            'selectedApps.*' => [
                'integer',
                'exists:apps,id',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Crear usuario
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $this->name,

            'email' => $this->email,

            'password' => Hash::make(
                $this->password
            ),

            'role' => $this->role,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Asignar aplicaciones
        |--------------------------------------------------------------------------
        */

        $user->apps()->sync(
            $this->selectedApps
        );


        /*
        |--------------------------------------------------------------------------
        | Mensaje
        |--------------------------------------------------------------------------
        */

        session()->flash(
            'success',
            'Usuario creado correctamente.'
        );


        /*
        |--------------------------------------------------------------------------
        | Regresar al listado
        |--------------------------------------------------------------------------
        */

        $this->redirect(
            route('admin.users.index'),
            navigate: true
        );
    }
};
?>

<div class="p-6">

    {{-- ENCABEZADO --}}

    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Crear usuario
        </h1>

        <p class="text-gray-500">
            Crea un usuario y asigna sus aplicaciones.
        </p>

    </div>


    {{-- FORMULARIO --}}

    <form wire:submit="save">

        {{-- NOMBRE --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Nombre
            </label>

            <input
                type="text"
                wire:model="name"
                placeholder="Nombre del usuario"
                class="w-full border rounded-lg px-4 py-2"
            >

            @error('name')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- EMAIL --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Correo electrónico
            </label>

            <input
                type="email"
                wire:model="email"
                placeholder="correo@ejemplo.com"
                class="w-full border rounded-lg px-4 py-2"
            >

            @error('email')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- CONTRASEÑA --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Contraseña
            </label>

            <input
                type="password"
                wire:model="password"
                placeholder="Mínimo 8 caracteres"
                class="w-full border rounded-lg px-4 py-2"
            >

            @error('password')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- CONFIRMAR CONTRASEÑA --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Confirmar contraseña
            </label>

            <input
                type="password"
                wire:model="password_confirmation"
                placeholder="Repite la contraseña"
                class="w-full border rounded-lg px-4 py-2"
            >

        </div>


        {{-- ROL --}}

        <div class="mb-6">

            <label class="block mb-1 font-medium">
                Rol
            </label>

            <select
                wire:model="role"
                class="w-full border rounded-lg px-4 py-2"
            >

                <option value="usuario">
                    Usuario
                </option>

                <option value="admin">
                    Administrador
                </option>

            </select>

            @error('role')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- APLICACIONES --}}

        <div class="mb-6">

            <h2 class="text-lg font-bold mb-3">
                Aplicaciones permitidas
            </h2>

            <p class="text-sm text-gray-500 mb-4">
                Selecciona las aplicaciones a las que tendrá acceso el usuario.
            </p>


            @php

                $apps = App::query()
                    ->where('active', true)
                    ->orderBy('name')
                    ->get();

            @endphp


            @forelse ($apps as $app)

                <label
                    wire:key="app-{{ $app->id }}"
                    class="flex items-center gap-3 border rounded-lg p-4 mb-2 cursor-pointer hover:bg-gray-50"
                >

                    <input
                        type="checkbox"
                        wire:model="selectedApps"
                        value="{{ $app->id }}"
                        class="w-4 h-4"
                    >

                    <div>

                        <div class="font-medium">
                            {{ $app->name }}
                        </div>

                        @if ($app->description)

                            <div class="text-sm text-gray-500">
                                {{ $app->description }}
                            </div>

                        @endif

                    </div>

                </label>

            @empty

                <div class="border rounded-lg p-4 text-gray-500">
                    No hay aplicaciones activas.
                </div>

            @endforelse


            @error('selectedApps')

                <div class="text-red-600 text-sm mt-2">
                    {{ $message }}
                </div>

            @enderror

            @error('selectedApps.*')

                <div class="text-red-600 text-sm mt-2">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- BOTONES --}}

        <div class="flex items-center gap-3">

            <a
                href="{{ route('admin.users.index') }}"
                wire:navigate
                class="px-4 py-2 border rounded-lg hover:bg-gray-50"
            >
                Cancelar
            </a>


            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >

                <span wire:loading.remove>
                    Crear usuario
                </span>

                <span wire:loading>
                    Guardando...
                </span>

            </button>

        </div>

    </form>

</div>

<?php

use App\Models\App;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'usuario';

    public array $selectedApps = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->name = $user->name;

        $this->email = $user->email;

        $this->role = $user->role;

        // Cargar aplicaciones que ya tiene asignadas
        $this->selectedApps = $user
            ->apps()
            ->pluck('apps.id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    public function update(): void
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
                'unique:users,email,' . $this->user->id,
            ],

            'password' => [
                'nullable',
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

        // Actualizar datos básicos
        $this->user->name = $this->name;

        $this->user->email = $this->email;

        $this->user->role = $this->role;

        // Cambiar contraseña solamente si escribieron una nueva
        if ($this->password !== '') {
            $this->user->password = Hash::make(
                $this->password
            );
        }

        $this->user->save();

        // Actualizar aplicaciones
        $this->user
            ->apps()
            ->sync($this->selectedApps);

        session()->flash(
            'success',
            'Usuario actualizado correctamente.'
        );

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
            Editar usuario
        </h1>

        <p class="text-gray-500">
            Modifica los datos y accesos del usuario.
        </p>

    </div>


    {{-- FORMULARIO --}}

    <form wire:submit="update">

        {{-- NOMBRE --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Nombre
            </label>

            <input
                type="text"
                wire:model="name"
                class="border rounded-lg w-full p-2"
            >

            @error('name')

                <span class="text-red-600 text-sm">
                    {{ $message }}
                </span>

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
                class="border rounded-lg w-full p-2"
            >

            @error('email')

                <span class="text-red-600 text-sm">
                    {{ $message }}
                </span>

            @enderror

        </div>


        {{-- ROL --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Rol
            </label>

            <select
                wire:model="role"
                class="border rounded-lg w-full p-2"
            >

                <option value="usuario">
                    Usuario
                </option>

                <option value="admin">
                    Administrador
                </option>

            </select>

            @error('role')

                <span class="text-red-600 text-sm">
                    {{ $message }}
                </span>

            @enderror

        </div>


        {{-- CONTRASEÑA --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Nueva contraseña
            </label>

            <input
                type="password"
                wire:model="password"
                placeholder="Dejar vacío para conservar la actual"
                class="border rounded-lg w-full p-2"
            >

            @error('password')

                <span class="text-red-600 text-sm">
                    {{ $message }}
                </span>

            @enderror

        </div>


        {{-- CONFIRMAR CONTRASEÑA --}}

        <div class="mb-6">

            <label class="block mb-1 font-medium">
                Confirmar nueva contraseña
            </label>

            <input
                type="password"
                wire:model="password_confirmation"
                class="border rounded-lg w-full p-2"
            >

        </div>


        {{-- APLICACIONES --}}

        <div class="mb-6">

            <h2 class="text-lg font-bold mb-3">
                Aplicaciones permitidas
            </h2>

            @php

                $apps = App::query()
                    ->where('active', true)
                    ->orderBy('name')
                    ->get();

            @endphp


            @forelse ($apps as $app)

                <label
                    wire:key="app-{{ $app->id }}"
                    class="flex items-center gap-3 border rounded-lg p-3 mb-2 cursor-pointer"
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

                <p class="text-gray-500">
                    No existen aplicaciones activas.
                </p>

            @endforelse


            @error('selectedApps.*')

                <span class="text-red-600 text-sm">
                    {{ $message }}
                </span>

            @enderror

        </div>


        {{-- BOTONES --}}

        <div class="flex gap-3">

            <a
                href="{{ route('admin.users.index') }}"
                wire:navigate
                class="px-4 py-2 border rounded-lg"
            >
                Cancelar
            </a>


            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg"
            >

                <span wire:loading.remove>
                    Guardar cambios
                </span>

                <span wire:loading>
                    Guardando...
                </span>

            </button>

        </div>

    </form>

</div>

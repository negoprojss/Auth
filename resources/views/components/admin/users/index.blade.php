<?php

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        // No permitir eliminar al usuario actual
        if ($user->id === auth()->id()) {
            session()->flash(
                'error',
                'No puedes eliminar tu propio usuario.'
            );

            return;
        }

        $user->delete();

        session()->flash(
            'success',
            'Usuario eliminado correctamente.'
        );
    }
};
?>

<div class="p-6">

    {{-- ENCABEZADO --}}
    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold">
                Usuarios
            </h1>

            <p class="text-gray-500">
                Administración de usuarios y accesos
            </p>

        </div>

        <a
            href="{{ route('admin.users.create') }}"
            wire:navigate
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            + Nuevo usuario
        </a>

    </div>


    {{-- MENSAJE ÉXITO --}}
    @if (session()->has('success'))

        <div
            class="mb-4 p-4 rounded-lg bg-green-100 text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif


    {{-- MENSAJE ERROR --}}
    @if (session()->has('error'))

        <div
            class="mb-4 p-4 rounded-lg bg-red-100 text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif


    {{-- BUSCADOR --}}
    <div class="mb-6">

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar usuario por nombre o correo..."
            class="w-full md:w-96 border rounded-lg px-4 py-2"
        >

    </div>


    {{-- TABLA --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">

        <table class="w-full">

            <thead>

                <tr class="bg-gray-100 border-b">

                    <th class="text-left p-4">
                        Usuario
                    </th>

                    <th class="text-left p-4">
                        Correo
                    </th>

                    <th class="text-left p-4">
                        Rol
                    </th>

                    <th class="text-left p-4">
                        Aplicaciones
                    </th>

                    <th class="text-center p-4">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody>

                @php

                    $users = User::query()
                        ->with('apps')
                        ->when(
                            $search !== '',
                            function ($query) {
                                $query->where(function ($query) {
                                    $query
                                        ->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        )
                                        ->orWhere(
                                            'email',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                });
                            }
                        )
                        ->orderBy('name')
                        ->paginate(10);

                @endphp


                @forelse ($users as $user)

                    <tr
                        wire:key="user-{{ $user->id }}"
                        class="border-b hover:bg-gray-50"
                    >

                        {{-- USUARIO --}}
                        <td class="p-4">

                            <div class="font-semibold">
                                {{ $user->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                ID: {{ $user->id }}
                            </div>

                        </td>


                        {{-- EMAIL --}}
                        <td class="p-4">

                            {{ $user->email }}

                        </td>


                        {{-- ROL --}}
                        <td class="p-4">

                            @if ($user->role === 'admin')

                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs bg-purple-100 text-purple-700"
                                >
                                    Administrador
                                </span>

                            @else

                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700"
                                >
                                    Usuario
                                </span>

                            @endif

                        </td>


                        {{-- APLICACIONES --}}
                        <td class="p-4">

                            @if ($user->apps->isNotEmpty())

                                <div class="flex flex-wrap gap-2">

                                    @foreach ($user->apps as $app)

                                        <span
                                            class="px-2 py-1 rounded bg-gray-100 text-sm"
                                        >
                                            {{ $app->name }}
                                        </span>

                                    @endforeach

                                </div>

                            @else

                                <span class="text-gray-400 text-sm">
                                    Sin aplicaciones
                                </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td class="p-4">

                            <div class="flex justify-center gap-2">

                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('admin.users.edit', $user->id) }}"
                                    wire:navigate
                                    class="px-3 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600"
                                >
                                    Editar
                                </a>


                                {{-- ELIMINAR --}}
                                @if ($user->id !== auth()->id())

                                    <button
                                        type="button"
                                        wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="¿Seguro que deseas eliminar este usuario?"
                                        class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700"
                                    >
                                        Eliminar
                                    </button>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="p-8 text-center text-gray-500"
                        >
                            No se encontraron usuarios.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINACIÓN --}}
    <div class="mt-6">

        {{ $users->links() }}

    </div>

</div>

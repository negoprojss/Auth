<?php

use App\Models\App;
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

    public function deleteApp(int $id): void
    {
        $app = App::findOrFail($id);

        // Eliminar las relaciones con usuarios
        $app->users()->detach();

        // Eliminar aplicación
        $app->delete();

        session()->flash(
            'success',
            'Aplicación eliminada correctamente.'
        );
    }
};
?>

<div class="p-6">

    {{-- ENCABEZADO --}}
    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold">
                Aplicaciones
            </h1>

            <p class="text-gray-500">
                Administración de aplicaciones y accesos
            </p>

        </div>

        <a
            href="{{ route('admin.apps.create') }}"
            wire:navigate
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            + Nueva aplicación
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
            placeholder="Buscar aplicación..."
            class="w-full md:w-96 border rounded-lg px-4 py-2"
        >

    </div>


    {{-- TABLA --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">

        <table class="w-full">

            <thead>

                <tr class="bg-gray-100 border-b">

                    <th class="text-left p-4">
                        Aplicación
                    </th>

                    <th class="text-left p-4">
                        Slug
                    </th>

                    <th class="text-left p-4">
                        URL
                    </th>

                    <th class="text-center p-4">
                        Usuarios
                    </th>

                    <th class="text-center p-4">
                        Estado
                    </th>

                    <th class="text-center p-4">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody>

                @php

                    $apps = App::query()
                        ->withCount('users')
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
                                            'slug',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                });
                            }
                        )
                        ->orderBy('name')
                        ->paginate(10);

                @endphp


                @forelse ($apps as $app)

                    <tr
                        wire:key="app-{{ $app->id }}"
                        class="border-b hover:bg-gray-50"
                    >

                        {{-- APLICACIÓN --}}

                        <td class="p-4">

                            <div class="font-semibold">
                                {{ $app->name }}
                            </div>

                            @if ($app->description)

                                <div class="text-sm text-gray-500">
                                    {{ $app->description }}
                                </div>

                            @endif

                        </td>


                        {{-- SLUG --}}

                        <td class="p-4">

                            <span class="text-sm">
                                {{ $app->slug }}
                            </span>

                        </td>


                        {{-- URL --}}

                        <td class="p-4">

                            @if ($app->url)

                                <a
                                    href="{{ $app->url }}"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ $app->url }}
                                </a>

                            @else

                                <span class="text-gray-400">
                                    Sin URL
                                </span>

                            @endif

                        </td>


                        {{-- USUARIOS --}}

                        <td class="p-4 text-center">

                            <span
                                class="inline-block px-3 py-1 rounded-full bg-gray-100"
                            >
                                {{ $app->users_count }}
                            </span>

                        </td>


                        {{-- ESTADO --}}

                        <td class="p-4 text-center">

                            @if ($app->active)

                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs bg-green-100 text-green-700"
                                >
                                    Activa
                                </span>

                            @else

                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs bg-red-100 text-red-700"
                                >
                                    Inactiva
                                </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}

                        <td class="p-4">

                            <div class="flex justify-center gap-2">

                                {{-- EDITAR --}}

                                <a
                                    href="{{ route('admin.apps.edit', $app->id) }}"
                                    wire:navigate
                                    class="px-3 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600"
                                >
                                    Editar
                                </a>


                                {{-- ELIMINAR --}}

                                <button
                                    type="button"
                                    wire:click="deleteApp({{ $app->id }})"
                                    wire:confirm="¿Seguro que deseas eliminar esta aplicación?"
                                    class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700"
                                >
                                    Eliminar
                                </button>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="p-8 text-center text-gray-500"
                        >
                            No se encontraron aplicaciones.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINACIÓN --}}

    <div class="mt-6">

        {{ $apps->links() }}

    </div>

</div>

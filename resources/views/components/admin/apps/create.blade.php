<?php

use App\Models\App;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $slug = '';

    public string $url = '';

    public string $description = '';

    public bool $active = true;


    /*
    |--------------------------------------------------------------------------
    | Generar slug automáticamente
    |--------------------------------------------------------------------------
    */

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }


    /*
    |--------------------------------------------------------------------------
    | Crear aplicación
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:apps,slug',
            ],

            'url' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'active' => [
                'boolean',
            ],
        ]);


        App::create([
            'name' => $this->name,

            'slug' => $this->slug,

            'url' => $this->url !== ''
                ? $this->url
                : null,

            'description' => $this->description !== ''
                ? $this->description
                : null,

            'active' => $this->active,
        ]);


        session()->flash(
            'success',
            'Aplicación creada correctamente.'
        );


        $this->redirect(
            route('admin.apps.index'),
            navigate: true
        );
    }
};
?>

<div class="p-6">

    {{-- ENCABEZADO --}}

    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Nueva aplicación
        </h1>

        <p class="text-gray-500">
            Registra una nueva aplicación en el sistema.
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
                wire:model.live="name"
                placeholder="Ejemplo: Inventario"
                class="w-full border rounded-lg px-4 py-2"
            >

            @error('name')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- SLUG --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Slug
            </label>

            <input
                type="text"
                wire:model="slug"
                placeholder="inventario"
                class="w-full border rounded-lg px-4 py-2"
            >

            <p class="text-xs text-gray-500 mt-1">
                Identificador único de la aplicación.
            </p>

            @error('slug')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- URL --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                URL
            </label>

            <input
                type="text"
                wire:model="url"
                placeholder="https://ejemplo.com"
                class="w-full border rounded-lg px-4 py-2"
            >

            <p class="text-xs text-gray-500 mt-1">
                URL a la que será enviado el usuario.
            </p>

            @error('url')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- DESCRIPCIÓN --}}

        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Descripción
            </label>

            <textarea
                wire:model="description"
                rows="4"
                placeholder="Descripción de la aplicación..."
                class="w-full border rounded-lg px-4 py-2"
            ></textarea>

            @error('description')

                <div class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- ESTADO --}}

        <div class="mb-6">

            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="checkbox"
                    wire:model="active"
                    class="w-4 h-4"
                >

                <span class="font-medium">
                    Aplicación activa
                </span>

            </label>

            <p class="text-xs text-gray-500 mt-1">
                Las aplicaciones inactivas no aparecerán para asignar
                nuevos accesos.
            </p>

        </div>


        {{-- BOTONES --}}

        <div class="flex items-center gap-3">

            <a
                href="{{ route('admin.apps.index') }}"
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
                    Crear aplicación
                </span>

                <span wire:loading>
                    Guardando...
                </span>

            </button>

        </div>

    </form>

</div>

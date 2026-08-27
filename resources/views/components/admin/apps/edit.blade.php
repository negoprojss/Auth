<?php

use App\Models\App;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public App $app;

    public string $name = '';

    public string $slug = '';

    public string $url = '';

    public string $description = '';

    public bool $active = true;


    /*
    |--------------------------------------------------------------------------
    | Cargar aplicación
    |--------------------------------------------------------------------------
    */

    public function mount(App $app): void
    {
        $this->app = $app;

        $this->name = $app->name;

        $this->slug = $app->slug;

        $this->url = $app->url ?? '';

        $this->description = $app->description ?? '';

        $this->active = (bool) $app->active;
    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar slug
    |--------------------------------------------------------------------------
    */

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar aplicación
    |--------------------------------------------------------------------------
    */

    public function update(): void
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
                'unique:apps,slug,' . $this->app->id,
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


        /*
        |--------------------------------------------------------------------------
        | Actualizar
        |--------------------------------------------------------------------------
        */

        $this->app->update([
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


        /*
        |--------------------------------------------------------------------------
        | Mensaje
        |--------------------------------------------------------------------------
        */

        session()->flash(
            'success',
            'Aplicación actualizada correctamente.'
        );


        /*
        |--------------------------------------------------------------------------
        | Regresar
        |--------------------------------------------------------------------------
        */

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
            Editar aplicación
        </h1>

        <p class="text-gray-500">
            Modifica la información de la aplicación.
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
                wire:model.live="name"
                placeholder="Nombre de la aplicación"
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
                placeholder="mi-aplicacion"
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
                Si está inactiva, no aparecerá para asignar nuevos accesos.
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
                    Guardar cambios
                </span>

                <span wire:loading>
                    Guardando...
                </span>

            </button>

        </div>

    </form>

</div>

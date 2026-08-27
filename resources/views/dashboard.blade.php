<x-layouts::app :title="__('Dashboard')">
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">
            Sobre el proyecto
        </h1>

        <p class="mt-3 text-zinc-600 dark:text-zinc-300">
            Sistema de gestión de biblioteca desarrollado en Laravel, Livewire y PostgreSQL.
            Permite administrar libros, autores y géneros mediante operaciones de creación,
            consulta, actualización y eliminación.
        </p>

        <p class="mt-3 text-zinc-600 dark:text-zinc-300">
            El proyecto incluye autenticación, validaciones, búsqueda, paginación y una API
            protegida para el acceso a la información.
        </p>

        <div class="mt-6">
            <a
                href="{{ route('books.index') }}"
                wire:navigate
                class="inline-flex items-center rounded-lg bg-zinc-800 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700"
            >
                Ir a libros
            </a>
        </div>
    </div>
    <!-- <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div> -->
</x-layouts::app>

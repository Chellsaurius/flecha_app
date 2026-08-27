<?php

use App\Models\Genre;
use App\Support\GenreValidationRules;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public ?int $editingGenreId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $validated = $this->validate(
            GenreValidationRules::rules($this->editingGenreId)
        );

        if ($this->editingGenreId !== null) {
            $genre = Genre::findOrFail($this->editingGenreId);

            $genre->update([
                'name' => $validated['name'],
            ]);

            session()->flash(
                'success',
                'Género actualizado correctamente.'
            );
        } else {
            Genre::create([
                'name' => $validated['name'],
            ]);

            session()->flash(
                'success',
                'Género creado correctamente.'
            );
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $genre = Genre::findOrFail($id);

        $this->editingGenreId = $genre->id;
        $this->name = $genre->name;

        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        $genre = Genre::findOrFail($id);

        if ($genre->books()->exists()) {
            session()->flash(
                'error',
                'No se puede eliminar el género porque tiene libros asociados.'
            );

            return;
        }

        $genre->delete();

        session()->flash(
            'success',
            'Género eliminado correctamente.'
        );

        $this->resetPage();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingGenreId',
            'name',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $genres = Genre::query()
            ->when(
                trim($this->search) !== '',
                fn ($query) => $query->where(
                    'name',
                    'ILIKE',
                    '%' . trim($this->search) . '%'
                )
            )
            ->orderBy('name')
            ->paginate(10);

        return $this->view([
            'genres' => $genres,
        ]);
    }
};
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-semibold">Géneros</h1>

        <p class="text-sm text-zinc-500">
            Administración de géneros registrados en la biblioteca.
        </p>
    </div>

    @if (session('success'))
        <div
            class="rounded-lg bg-green-100 px-4 py-3 text-green-800
                   dark:bg-green-900 dark:text-green-100"
        >
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            class="rounded-lg bg-red-100 px-4 py-3 text-red-800
                   dark:bg-red-900 dark:text-red-100"
        >
            {{ session('error') }}
        </div>
    @endif

    <div
        class="rounded-lg border border-zinc-200 p-6
               dark:border-zinc-700"
    >
        <h2 class="mb-4 text-lg font-semibold">
            {{ $editingGenreId ? 'Editar género' : 'Nuevo género' }}
        </h2>

        <form wire:submit="save" class="space-y-4">

            <div>
                <label class="block text-sm font-medium">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="name"
                    class="mt-1 w-full rounded-lg border
                           border-zinc-300 px-4 py-2
                           dark:border-zinc-700 dark:bg-zinc-900"
                >

                @error('name')
                    <span class="text-sm text-red-600">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="flex gap-2">

                <button
                    type="submit"
                    class="rounded-lg bg-zinc-800 px-4 py-2
                           text-sm text-white hover:bg-zinc-700"
                >
                    {{ $editingGenreId
                        ? 'Actualizar género'
                        : 'Crear género' }}
                </button>

                @if ($editingGenreId)
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        class="rounded-lg border border-zinc-300
                               px-4 py-2 text-sm
                               dark:border-zinc-700"
                    >
                        Cancelar
                    </button>
                @endif

            </div>

        </form>
    </div>

    <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="Buscar género..."
        class="w-full rounded-lg border border-zinc-300
               px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
    >

    <div
        class="overflow-x-auto rounded-lg border
               border-zinc-200 dark:border-zinc-700"
    >
        <table class="w-full text-left text-sm">

            <thead
                class="bg-zinc-100 text-zinc-900
                       dark:bg-zinc-800 dark:text-white"
            >
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($genres as $genre)
                    <tr
                        class="border-t border-zinc-200
                               dark:border-zinc-700"
                    >
                        <td class="px-4 py-3">
                            {{ $genre->name }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex gap-2">

                                <button
                                    type="button"
                                    wire:click="edit({{ $genre->id }})"
                                    class="rounded-lg bg-zinc-800
                                           px-3 py-1.5 text-sm
                                           text-white hover:bg-zinc-700"
                                >
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    wire:click="delete({{ $genre->id }})"
                                    wire:confirm="¿Estás seguro de eliminar este género?"
                                    class="rounded-lg bg-red-600
                                           px-3 py-1.5 text-sm
                                           text-white hover:bg-red-500"
                                >
                                    Eliminar
                                </button>

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td
                            colspan="2"
                            class="px-4 py-6 text-center
                                   text-zinc-500"
                        >
                            No se encontraron géneros.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div>
        {{ $genres->links() }}
    </div>

</div>
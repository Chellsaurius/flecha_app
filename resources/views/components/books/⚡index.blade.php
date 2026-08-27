<?php

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Author;
use App\Models\Genre;
use App\Services\BookCodeGenerator;
use App\Support\BookValidationRules;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $title = '';
    public ?int $author_id = null;
    public ?int $genre_id = null;
    public ?int $publication_year = null;
    public string $description = '';
    public ?int $editingBookId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $validated = $this->validate(BookValidationRules::rules());

        if ($this->editingBookId !== null) {
            $book = Book::findOrFail($this->editingBookId);

            $book->update([
                'title' => $validated['title'],
                'author_id' => $validated['author_id'],
                'genre_id' => $validated['genre_id'],
                'publication_year' => $validated['publication_year'],
                'description' => $validated['description'],
            ]);

            session()->flash('success', 'Libro actualizado correctamente.');
        } else {
            Book::create([
                'book_code' => app(BookCodeGenerator::class)->generate(),
                'title' => $validated['title'],
                'author_id' => $validated['author_id'],
                'genre_id' => $validated['genre_id'],
                'publication_year' => $validated['publication_year'],
                'description' => $validated['description'],
            ]);

            session()->flash('success', 'Libro creado correctamente.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function render()
    {
        $authors = Author::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        $books = Book::with(['author', 'genre'])
            ->when(
                $this->search !== '',
                fn ($query) => $query->search($this->search)
            )
            ->orderBy('title')
            ->paginate(10);

            return $this->view([
            'books' => $books,
            'authors' => $authors,
            'genres' => $genres,
        ]);
        
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $book = Book::findOrFail($id);

        $book->delete();

        // Para implementar borrado lógico:
        // $book->update(['is_active' => false]);

        $this->resetPage();

        session()->flash('success', 'Libro eliminado correctamente.');
    }

    public function edit(int $id): void
    {
        $book = Book::findOrFail($id);

        $this->editingBookId = $book->id;
        $this->title = $book->title;
        $this->author_id = $book->author_id;
        $this->genre_id = $book->genre_id;
        $this->publication_year = $book->publication_year;
        $this->description = $book->description ?? '';

        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingBookId',
            'title',
            'author_id',
            'genre_id',
            'publication_year',
            'description',
        ]);

        $this->resetValidation();
    }

};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Libros</h1>
        <p class="text-sm text-zinc-500">
            Catálogo de libros registrados.
        </p>
    </div>

    <div>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar por título, autor o género..."
            class="w-full rounded-lg border border-zinc-300 px-4 py-2"
        >
    </div>

    <div class="rounded-lg border border-zinc-200 p-6 dark:border-zinc-700">
        <h2 class="mb-4 text-lg font-semibold">
            {{ $editingBookId ? 'Editar libro' : 'Nuevo libro' }}
        </h2>

        @if (session('success'))
            <div class="rounded-lg bg-green-100 px-4 py-3 text-green-800 dark:bg-green-900 dark:text-green-100">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">
                    Título
                </label>

                <input
                    type="text"
                    wire:model="title"
                    class="mt-1 w-full rounded-lg border border-zinc-300 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                >

                @error('title')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Autor
                </label>

                <select
                    wire:model="author_id"
                    class="mt-1 w-full rounded-lg border border-zinc-300 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                >
                    <option value="">Selecciona un autor</option>

                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}">
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>

                @error('author_id')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Género
                </label>

                <select
                    wire:model="genre_id"
                    class="mt-1 w-full rounded-lg border border-zinc-300 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                >
                    <option value="">Selecciona un género</option>

                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}">
                            {{ $genre->name }}
                        </option>
                    @endforeach
                </select>

                @error('genre_id')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Año de publicación
                </label>

                <input
                    type="number"
                    inputmode="numeric"
                    min="1"
                    max="{{ now()->year }}"
                    wire:model="publication_year"
                    class="mt-1 w-full rounded-lg border border-zinc-300 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                >

                @error('publication_year')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Descripción
                </label>

                <textarea
                    wire:model="description"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-zinc-300 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-900"
                ></textarea>

                @error('description')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="rounded-lg bg-zinc-800 px-4 py-2 text-sm text-white hover:bg-zinc-700"
                >
                    {{ $editingBookId ? 'Actualizar libro' : 'Crear libro' }}
                </button>
                @if ($editingBookId)
                    <button
                        type="button"
                        wire:click="cancelEdit"
                        class="rounded-lg border border-zinc-300 px-4 py-2 text-sm dark:border-zinc-700"
                    >
                        Cancelar
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white">
                <tr>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Autor</th>
                    <th class="px-4 py-3">Género</th>
                    <th class="px-4 py-3">Año</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($books as $book)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $book->book_code }}</td>
                        <td class="px-4 py-3">{{ $book->title }}</td>
                        <td class="px-4 py-3">{{ $book->author->name }}</td>
                        <td class="px-4 py-3">{{ $book->genre->name }}</td>
                        <td class="px-4 py-3">{{ $book->publication_year }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    wire:click="edit({{ $book->id }})"
                                    class="rounded-lg bg-zinc-800 px-3 py-1.5 text-sm text-white hover:bg-zinc-700"
                                >
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    wire:click="delete({{ $book->id }})"
                                    wire:confirm="¿Estás seguro de eliminar este libro?"
                                    class="rounded-lg bg-red-600 px-3 py-1.5 text-sm text-white hover:bg-red-500"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-zinc-500">
                            No se encontraron libros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $books->links() }}
    </div>
</div>
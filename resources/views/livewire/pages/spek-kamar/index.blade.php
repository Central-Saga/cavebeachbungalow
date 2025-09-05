<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\TipeKamar;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public $search = '';
    public $showDeleteModal = false;
    public $tipeToDelete = null;
    public $selectedTipe = null;

    public function updatedSearch() { $this->resetPage(); }

    public function getTipeKamarsProperty() {
        return TipeKamar::with('fasilitasKamars')
            ->when($this->search, function ($query) {
                $query->where('nama_tipe', 'like', "%{$this->search}%")
                    ->orWhereHas('fasilitasKamars', fn($q) => $q->where('nama_fasilitas', 'like', "%{$this->search}%"));
            })
            ->orderBy('nama_tipe')
            ->paginate(10);
    }

    public function delete($id) {
        $this->tipeToDelete = TipeKamar::find($id);
        $this->showDeleteModal = true;
    }

    public function confirmDelete() {
        if ($this->tipeToDelete) {
            $this->tipeToDelete->fasilitasKamars()->detach();
            $this->tipeToDelete->delete();
            session()->flash('message', 'Tipe kamar dan fasilitas terkait berhasil dihapus.');
            $this->cancelDelete();
        }
    }

    public function cancelDelete() {
        $this->showDeleteModal = false;
        $this->tipeToDelete = null;
    }

    public function viewTipe($id) {
        $this->selectedTipe = TipeKamar::with('fasilitasKamars')->find($id);
    }

    public function closeTipeModal() {
        $this->selectedTipe = null;
    }
}; ?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kelola Spesifikasi Kamar</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Kelola tipe kamar dan fasilitas yang tersedia untuk setiap tipe kamar
                    </p>
                </div>
                <a href="{{ route('spek-kamar.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Spesifikasi
                </a>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live="search" type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-pink-400"
                    placeholder="Cari tipe kamar atau fasilitas...">
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
        <div
            class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
            {{ session('message') }}
        </div>
        @endif

        <!-- Error Message -->
        @if (session()->has('error'))
        <div
            class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
            {{ session('error') }}
        </div>
        @endif

        <!-- Table -->
        <div
            class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tipe Kamar
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Fasilitas
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Dibuat
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->tipeKamars as $tipe)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-r from-pink-400 to-rose-400 flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">
                                                {{ strtoupper(substr($tipe->nama_tipe, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $tipe->nama_tipe }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($tipe->fasilitasKamars as $fasilitas)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                        {{ $fasilitas->nama_fasilitas }}
                                    </span>
                                    @empty
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                        Tidak ada fasilitas
                                    </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $tipe->created_at ? $tipe->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="viewTipe({{ $tipe->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('spek-kamar.edit', $tipe->id) }}"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <button wire:click="delete({{ $tipe->id }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada tipe
                                        kamar</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan membuat tipe kamar
                                        pertama Anda.</p>
                                    <a href="{{ route('spek-kamar.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Tambah Tipe Kamar Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->tipeKamars->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $this->tipeKamars->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 backdrop-blur-sm bg-black/20 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
        wire:click="cancelDelete">
        <div class="relative mx-auto p-6 border w-96 shadow-2xl rounded-xl bg-white dark:bg-gray-800 backdrop-blur-md"
            wire:click.stop>
            <div class="text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Konfirmasi Hapus</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus tipe kamar <strong>{{ $tipeToDelete->nama_tipe ?? ''
                            }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <div class="flex justify-center space-x-3">
                        <button wire:click="cancelDelete"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                            Batal
                        </button>
                        <button wire:click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tipe Kamar Detail Modal -->
    @if($selectedTipe)
    <div class="fixed inset-0 backdrop-blur-sm bg-black/20 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
        wire:click="closeTipeModal">
        <div class="relative mx-auto p-6 border w-11/12 max-w-2xl shadow-2xl rounded-xl bg-white dark:bg-gray-800 backdrop-blur-md"
            wire:click.stop>
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Detail Tipe Kamar: {{ $selectedTipe->nama_tipe }}
                    </h3>
                    <button wire:click="closeTipeModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Tipe
                            Kamar</label>
                        <p
                            class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
                            {{ $selectedTipe->nama_tipe }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fasilitas ({{
                            $selectedTipe->fasilitasKamars->count() }})</label>
                        <div class="bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
                            @if($selectedTipe->fasilitasKamars->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedTipe->fasilitasKamars as $fasilitas)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ $fasilitas->nama_fasilitas }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada fasilitas</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dibuat</label>
                            <p
                                class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
                                {{ $selectedTipe->created_at ? $selectedTipe->created_at->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Diperbarui</label>
                            <p
                                class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-md">
                                {{ $selectedTipe->updated_at ? $selectedTipe->updated_at->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button wire:click="closeTipeModal"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Tutup
                    </button>
                    <a href="{{ route('spek-kamar.edit', $selectedTipe->id) }}"
                        class="px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-md hover:from-pink-600 hover:to-rose-600 transition-all duration-200">
                        Edit Spesifikasi
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
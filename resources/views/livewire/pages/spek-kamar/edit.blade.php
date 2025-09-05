<?php
use Livewire\Volt\Component;
use App\Models\FasilitasKamar;
use App\Models\TipeKamar;
use App\Models\SpekKamar;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component {
    public $tipe_kamar_id = '';
    public $fasilitas_kamar_ids = [];

    public function mount($id) {
        $tipe = TipeKamar::findOrFail($id);
        $this->tipe_kamar_id = $tipe->id;
        $this->fasilitas_kamar_ids = $tipe->fasilitasKamars->pluck('id')->toArray();
    }

    public function update() {
        $this->validate([
            'fasilitas_kamar_ids' => 'required|array|min:1',
            'fasilitas_kamar_ids.*' => 'exists:fasilitas_kamars,id',
            'tipe_kamar_id' => 'required|exists:tipe_kamars,id',
        ]);

        $tipe = TipeKamar::findOrFail($this->tipe_kamar_id);
        $tipe->fasilitasKamars()->sync($this->fasilitas_kamar_ids);

        session()->flash('message', 'Fasilitas tipe kamar berhasil diperbarui!');
        return redirect()->route('spek-kamar.index');
    }

    public function getFasilitasProperty() {
        return FasilitasKamar::orderBy('nama_fasilitas')->get();
    }
    public function getTipesProperty() {
        return TipeKamar::orderBy('nama_tipe')->get();
    }
};
?>

<div class="py-6">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Spek Kamar</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Perbarui relasi tipe kamar dan fasilitas.</p>
        </div>
        <form wire:submit.prevent="update"
            class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-8 border border-gray-200 dark:border-gray-700">
            <div class="mb-6">
                <label for="tipe_kamar_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe
                    Kamar</label>
                <input type="text" readonly
                    value="{{ optional(App\Models\TipeKamar::find($tipe_kamar_id))->nama_tipe }}"
                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
            <div class="mb-6">
                <label for="fasilitas_kamar_ids"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fasilitas (bisa pilih lebih
                    dari satu)</label>
                <select wire:model.defer="fasilitas_kamar_ids" id="fasilitas_kamar_ids" multiple
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach($this->fasilitas as $fasilitasItem)
                    <option value="{{ $fasilitasItem->id }}" @selected(in_array($fasilitasItem->id,
                        $fasilitas_kamar_ids))>{{
                        $fasilitasItem->nama_fasilitas }}</option>
                    @endforeach
                </select>
                @error('fasilitas_kamar_ids')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
            </div>
            <div class="flex justify-end">
                <a href="{{ route('spek-kamar.index') }}"
                    class="mr-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transition-all duration-200">Simpan</button>
            </div>
        </form>
    </div>
</div>
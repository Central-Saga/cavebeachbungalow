<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\FasilitasKamar;

class FasilitasKamarSeeder extends Seeder
{
    public function run(): void
    {
        FasilitasKamar::factory()->count(5)->create();
    }
}

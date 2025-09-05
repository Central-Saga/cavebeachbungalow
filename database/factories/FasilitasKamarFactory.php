<?php


namespace Database\Factories;
use App\Models\FasilitasKamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class FasilitasKamarFactory extends Factory
{
    protected $model = FasilitasKamar::class;

    public function definition(): array
    {
        return [
            'nama_fasilitas' => $this->faker->word(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Yogyakarta', 'Malang'];

        return [
            'user_id' => User::factory(),
            'alamat' => 'Jl. ' . fake()->randomElement(['Sudirman', 'Thamrin', 'Gatot Subroto', 'Jendral Ahmad Yani', 'Gajah Mada']) . ' No. ' . fake()->numberBetween(1, 999),
            'kota' => fake()->randomElement($cities),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tanggal_lahir' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'telepon' => '(+62) ' . fake()->numberBetween(800, 899) . ' ' . fake()->numberBetween(1000, 9999) . ' ' . fake()->numberBetween(1000, 9999),
        ];
    }

    /**
     * Indicate that the pelanggan is male.
     */
    public function male(): static
    {
        return $this->state(fn(array $attributes) => [
            'jenis_kelamin' => 'L',
        ]);
    }

    /**
     * Indicate that the pelanggan is female.
     */
    public function female(): static
    {
        return $this->state(fn(array $attributes) => [
            'jenis_kelamin' => 'P',
        ]);
    }

    /**
     * Indicate that the pelanggan is from specific city.
     */
    public function fromCity(string $city): static
    {
        return $this->state(fn(array $attributes) => [
            'kota' => $city,
        ]);
    }
}

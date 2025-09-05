<?php

use App\Models\Pelanggan;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('can render pelanggan index page', function () {
    $component = Volt::test('pages.pelanggan.index');
    
    $component->assertOk();
});

it('can render pelanggan create page', function () {
    $component = Volt::test('pages.pelanggan.create');
    
    $component->assertOk();
});

it('can create a new pelanggan', function () {
    $userData = [
        'nama_lengkap' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'telepon' => '081234567890',
    ];

    $component = Volt::test('pages.pelanggan.create')
        ->set('nama_lengkap', $userData['nama_lengkap'])
        ->set('email', $userData['email'])
        ->set('telepon', $userData['telepon'])
        ->call('save');

    $component->assertRedirect(route('pelanggan.index'));
    
    expect(Pelanggan::where('email', $userData['email'])->exists())->toBeTrue();
});

it('can edit an existing pelanggan', function () {
    $pelanggan = Pelanggan::factory()->create();
    
    $component = Volt::test('pages.pelanggan.edit', ['pelanggan' => $pelanggan]);
    
    $component->assertOk()
        ->assertSet('nama_lengkap', $pelanggan->nama_lengkap)
        ->assertSet('email', $pelanggan->email)
        ->assertSet('telepon', $pelanggan->telepon);
});

it('can update pelanggan data', function () {
    $pelanggan = Pelanggan::factory()->create();
    
    $newData = [
        'nama_lengkap' => 'Nama Baru',
        'email' => 'emailbaru@example.com',
        'telepon' => '089876543210',
    ];

    $component = Volt::test('pages.pelanggan.edit', ['pelanggan' => $pelanggan])
        ->set('nama_lengkap', $newData['nama_lengkap'])
        ->set('email', $newData['email'])
        ->set('telepon', $newData['telepon'])
        ->call('save');

    $component->assertRedirect(route('pelanggan.index'));
    
    $pelanggan->refresh();
    expect($pelanggan->nama_lengkap)->toBe($newData['nama_lengkap']);
    expect($pelanggan->email)->toBe($newData['email']);
    expect($pelanggan->telepon)->toBe($newData['telepon']);
});

it('can delete a pelanggan', function () {
    $pelanggan = Pelanggan::factory()->create();
    
    $component = Volt::test('pages.pelanggan.index')
        ->call('deletePelanggan', $pelanggan->id)
        ->call('confirmDelete');

    expect(Pelanggan::find($pelanggan->id))->toBeNull();
});

it('can search pelanggans', function () {
    $pelanggan1 = Pelanggan::factory()->create(['nama_lengkap' => 'John Doe']);
    $pelanggan2 = Pelanggan::factory()->create(['nama_lengkap' => 'Jane Smith']);
    
    $component = Volt::test('pages.pelanggan.index')
        ->set('search', 'John');
    
    expect($component->get('pelanggans')->items())->toHaveCount(1);
    expect($component->get('pelanggans')->items()[0]->id)->toBe($pelanggan1->id);
});

it('validates required fields when creating pelanggan', function () {
    $component = Volt::test('pages.pelanggan.create')
        ->set('nama_lengkap', '')
        ->set('email', '')
        ->set('telepon', '')
        ->call('save');

    $component->assertHasErrors(['nama_lengkap', 'email', 'telepon']);
});

it('validates email uniqueness when creating pelanggan', function () {
    $existingPelanggan = Pelanggan::factory()->create();
    
    $component = Volt::test('pages.pelanggan.create')
        ->set('nama_lengkap', 'Test Name')
        ->set('email', $existingPelanggan->email)
        ->set('telepon', '081234567890')
        ->call('save');

    $component->assertHasErrors(['email']);
});

it('can associate pelanggan with user', function () {
    $user = User::factory()->create();
    
    $component = Volt::test('pages.pelanggan.create')
        ->set('user_id', $user->id)
        ->set('nama_lengkap', 'Test Name')
        ->set('email', 'test@example.com')
        ->set('telepon', '081234567890')
        ->call('save');

    $pelanggan = Pelanggan::where('email', 'test@example.com')->first();
    expect($pelanggan->user_id)->toBe($user->id);
    expect($pelanggan->user->id)->toBe($user->id);
});

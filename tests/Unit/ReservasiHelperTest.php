<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\ReservasiHelper;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservasiHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_kode_reservasi_format()
    {
        $kode = ReservasiHelper::generateKodeReservasi();

        // Test format: RSV + YYYY + MM + 4 digit
        $this->assertMatchesRegularExpression('/^RSV\d{4}\d{2}\d{4}$/', $kode);

        // Test panjang total (3 + 4 + 2 + 4 = 13 karakter)
        $this->assertEquals(13, strlen($kode));

        // Test prefix
        $this->assertStringStartsWith('RSV', $kode);
    }

    public function test_generate_kode_reservasi_with_custom_prefix()
    {
        $kode = ReservasiHelper::generateKodeReservasiWithPrefix('BOOK');

        // Test format dengan prefix custom
        $this->assertMatchesRegularExpression('/^BOOK\d{4}\d{2}\d{4}$/', $kode);

        // Test panjang total (4 + 4 + 2 + 4 = 14 karakter)
        $this->assertEquals(14, strlen($kode));

        // Test prefix
        $this->assertStringStartsWith('BOOK', $kode);
    }

    public function test_validate_kode_reservasi()
    {
        // Test valid codes
        $this->assertTrue(ReservasiHelper::validateKodeReservasi('RSV2025010001'));
        $this->assertTrue(ReservasiHelper::validateKodeReservasi('RSV2025129999'));

        // Test invalid codes
        $this->assertFalse(ReservasiHelper::validateKodeReservasi('RSV202501001')); // Kurang 1 digit
        $this->assertFalse(ReservasiHelper::validateKodeReservasi('RSV20250100001')); // Lebih 1 digit
        $this->assertFalse(ReservasiHelper::validateKodeReservasi('RSV2025010001A')); // Ada huruf
        $this->assertFalse(ReservasiHelper::validateKodeReservasi('BOOK2025010001')); // Prefix salah
    }

    public function test_parse_kode_reservasi()
    {
        $kode = 'RSV2025010001';
        $info = ReservasiHelper::parseKodeReservasi($kode);

        $this->assertIsArray($info);
        $this->assertEquals('2025', $info['year']);
        $this->assertEquals('01', $info['month']);
        $this->assertEquals(1, $info['number']);
        $this->assertArrayHasKey('formatted', $info);
    }

    public function test_parse_invalid_kode_reservasi()
    {
        $info = ReservasiHelper::parseKodeReservasi('INVALID');

        $this->assertIsArray($info);
        $this->assertEmpty($info);
    }

    public function test_generate_sequential_codes()
    {
        // Test bahwa kode yang di-generate memiliki format yang benar
        // dan dapat di-parse dengan benar

        $kode1 = ReservasiHelper::generateKodeReservasi();
        $kode2 = ReservasiHelper::generateKodeReservasi();
        $kode3 = ReservasiHelper::generateKodeReservasi();

        // Test format semua kode valid
        $this->assertTrue(ReservasiHelper::validateKodeReservasi($kode1));
        $this->assertTrue(ReservasiHelper::validateKodeReservasi($kode2));
        $this->assertTrue(ReservasiHelper::validateKodeReservasi($kode3));

        // Test bahwa semua kode memiliki format yang sama
        $this->assertEquals(13, strlen($kode1));
        $this->assertEquals(13, strlen($kode2));
        $this->assertEquals(13, strlen($kode3));

        // Test bahwa semua kode dimulai dengan RSV
        $this->assertStringStartsWith('RSV', $kode1);
        $this->assertStringStartsWith('RSV', $kode2);
        $this->assertStringStartsWith('RSV', $kode3);
    }
}

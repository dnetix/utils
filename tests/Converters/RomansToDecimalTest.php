<?php

namespace Tests\Converters;

use Dnetix\Converters\RomanConverter;
use Exception;
use Tests\TestCase;

class RomansToDecimalTest extends TestCase
{
    public function convertRoman($roman)
    {
        return RomanConverter::load($roman)->toDecimal();
    }

    public function testItParsesCorrectlySimpleNumbers()
    {
        $this->assertEquals(1, $this->convertRoman('I'));

        $this->assertEquals(500, $this->convertRoman('D'));

        $this->assertEquals(50, $this->convertRoman('L'));
    }

    public function testItAcceptsLowercaseRomanNumbers()
    {
        $this->assertEquals(1, $this->convertRoman('i'));

        $this->assertEquals(500, $this->convertRoman('d'));
    }

    public function testItThrowsExceptionWhenTheRomanNumberItsMalformed()
    {
        $this->expectException(Exception::class);
        $this->convertRoman('MCMZI');
    }

    public function testItClearsWhiteSpacesGivenAndParsesCorrectly()
    {
        $this->assertEquals(1500, $this->convertRoman('  M D '));
    }

    public function testItParsesLargeRomanNumbers()
    {
        $this->assertEquals(1900, $this->convertRoman('MCM'));
        $this->assertEquals(79, $this->convertRoman('LXXIX'));
        $this->assertEquals(1987, $this->convertRoman('MCMLXXXVII'));
        $this->assertEquals(887, $this->convertRoman('DCCCLXXXVII'));
        $this->assertEquals(749, $this->convertRoman('DCCXLIX'));
    }

    public function testItThrowsExceptionWhenNoProviderExists()
    {
        $this->expectException(Exception::class);
        RomanConverter::load('XI')->toHexadecimal();
    }

    public function testItThrowsExceptionWhenNoConversionNotationUsed()
    {
        $this->expectException(Exception::class);
        RomanConverter::load('XI')->decimal();
    }
}

<?php

namespace Tests\Converters;

use Dnetix\Converters\DecimalConverter;
use Exception;
use Tests\TestCase;

class DecimalToRomansTest extends TestCase
{
    public function convertDecimal($decimal)
    {
        return DecimalConverter::load($decimal)->toRomans();
    }

    public function testItParsesCorrectlySimpleNumbers()
    {
        $this->assertEquals('MCMXX', $this->convertDecimal(1920));

        $this->assertEquals('LVIII', $this->convertDecimal(58));

        $this->assertEquals('IX', $this->convertDecimal(9));

        $this->assertEquals('I', $this->convertDecimal(1));
    }

    public function testItParsesIntegersPassedAsStrings()
    {
        $this->assertEquals('MMMMMDCCCLXXVI', $this->convertDecimal('5876'));
    }

    public function testItThrowsExceptionWhenNoNumberPassed()
    {
        $this->expectException(Exception::class);
        DecimalConverter::load('ABCD');
    }
}

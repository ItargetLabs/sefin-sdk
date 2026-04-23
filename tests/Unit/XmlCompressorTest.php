<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Exception\ValidationException;
use SefinSdk\Support\XmlCompressor;

final class XmlCompressorTest extends TestCase
{
    public function testEncodeAndDecodeXml(): void
    {
        $xml = '<DPS><Id>123</Id></DPS>';

        $encoded = XmlCompressor::encode($xml);

        self::assertNotSame($xml, $encoded);
        self::assertSame($xml, XmlCompressor::decode($encoded));
    }

    public function testDecodeThrowsExceptionForInvalidPayload(): void
    {
        $this->expectException(ValidationException::class);

        XmlCompressor::decode('invalid');
    }
}

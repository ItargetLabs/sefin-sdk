<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Support\XmlNormalizer;

final class XmlNormalizerTest extends TestCase
{
    public function testAddsUtf8PrologWhenMissing(): void
    {
        $xml = '<DPS><Teste>1</Teste></DPS>';

        $normalized = XmlNormalizer::ensureUtf8($xml);

        self::assertStringStartsWith('<?xml', $normalized);
        self::assertStringContainsString('encoding="UTF-8"', $normalized);
        self::assertStringContainsString('<DPS>', $normalized);
    }

    public function testForcesUtf8EncodingInExistingProlog(): void
    {
        $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<DPS></DPS>";

        $normalized = XmlNormalizer::ensureUtf8($xml);

        self::assertStringContainsString('encoding="UTF-8"', $normalized);
        self::assertStringNotContainsString('ISO-8859-1', $normalized);
    }
}


<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Support\DanfsePdfGenerator;

final class DanfsePdfGeneratorTest extends TestCase
{
    public function testGeneratesPdfFromFixtureNfseXml(): void
    {
        $path = self::resolveNfseXmlPath();
        $xml = (string) file_get_contents($path);

        $pdf = (new DanfsePdfGenerator())->generateFromXml($xml);

        self::assertNotSame('', $pdf);
        self::assertStringStartsWith('%PDF', $pdf);
        self::assertGreaterThan(5000, strlen($pdf), 'PDF deve ter tamanho mínimo plausível');

        $buildDir = dirname(__DIR__, 2) . '/build';
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0775, true);
        }
        $pdfPath = $buildDir . '/danfse-from-fixture.pdf';
        self::assertNotFalse(file_put_contents($pdfPath, $pdf), 'Deve gravar o PDF em disco');
        self::assertFileExists($pdfPath);
        self::assertGreaterThan(5000, filesize($pdfPath) ?: 0);
    }

    public function testRejectsEmptyXml(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('empty');

        (new DanfsePdfGenerator())->generateFromXml('   ');
    }

    /**
     * Usa `nfse.xml` na raiz do repositório quando existir; caso contrário o fixture versionado em tests/fixtures.
     */
    private static function resolveNfseXmlPath(): string
    {
        $root = dirname(__DIR__, 2) . '/nfse.xml';
        if (is_readable($root)) {
            return $root;
        }

        $fixture = dirname(__DIR__, 2) . '/tests/fixtures/nfse.xml';
        if (! is_readable($fixture)) {
            self::fail('Defina nfse.xml na raiz ou mantenha tests/fixtures/nfse.xml.');
        }

        return $fixture;
    }
}

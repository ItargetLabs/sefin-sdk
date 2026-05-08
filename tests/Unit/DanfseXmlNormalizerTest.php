<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Support\DanfseXmlNormalizer;

final class DanfseXmlNormalizerTest extends TestCase
{
    public function testRemovesTotTribNodesInNfseNamespace(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<NFSe versao="1.0" xmlns="http://www.sped.fazenda.gov.br/nfse">
  <infNFSe Id="X">
    <nNFSe>1</nNFSe>
    <trib>
      <totTrib>
        <vTotTrib><vTotTribFed>1.00</vTotTribFed></vTotTrib>
      </totTrib>
    </trib>
  </infNFSe>
</NFSe>
XML;

        $out = DanfseXmlNormalizer::prepareForDanfseMapper($xml);

        self::assertStringNotContainsString('<totTrib>', $out);
        self::assertStringContainsString('<nNFSe>1</nNFSe>', $out);
    }
}

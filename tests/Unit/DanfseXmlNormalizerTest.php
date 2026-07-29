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

    public function testInjectsIssqnFieldsFromValoresNfseIntoTribMun(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<NFSe versao="1.01" xmlns="http://www.sped.fazenda.gov.br/nfse">
  <infNFSe Id="NFS1">
    <valores>
      <vBC>100.00</vBC>
      <pAliqAplic>2.00</pAliqAplic>
      <vISSQN>2.00</vISSQN>
      <vLiq>100.00</vLiq>
    </valores>
    <DPS versao="1.01">
      <infDPS Id="DPS1">
        <valores>
          <vServPrest><vServ>100</vServ></vServPrest>
          <trib>
            <tribMun>
              <tribISSQN>1</tribISSQN>
              <tpRetISSQN>1</tpRetISSQN>
            </tribMun>
          </trib>
        </valores>
      </infDPS>
    </DPS>
  </infNFSe>
</NFSe>
XML;

        $out = DanfseXmlNormalizer::prepareForDanfseMapper($xml);

        self::assertMatchesRegularExpression(
            '#<tribMun>.*<vBC>100\.00</vBC>.*</tribMun>#s',
            $out
        );
        self::assertMatchesRegularExpression(
            '#<tribMun>.*<pAliq>2\.00</pAliq>.*</tribMun>#s',
            $out
        );
        self::assertMatchesRegularExpression(
            '#<tribMun>.*<vISSQN>2\.00</vISSQN>.*</tribMun>#s',
            $out
        );
        // Não sobrescreve pAliqAplic no bloco da NFSe.
        self::assertStringContainsString('<pAliqAplic>2.00</pAliqAplic>', $out);
    }

    public function testDoesNotOverwriteExistingTribMunIssqnFields(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<NFSe versao="1.01" xmlns="http://www.sped.fazenda.gov.br/nfse">
  <infNFSe Id="NFS1">
    <valores>
      <vBC>100.00</vBC>
      <pAliqAplic>2.00</pAliqAplic>
      <vISSQN>2.00</vISSQN>
    </valores>
    <DPS versao="1.01">
      <infDPS Id="DPS1">
        <valores>
          <trib>
            <tribMun>
              <tribISSQN>1</tribISSQN>
              <pAliq>5.00</pAliq>
              <vBC>50.00</vBC>
              <vISSQN>2.50</vISSQN>
              <tpRetISSQN>1</tpRetISSQN>
            </tribMun>
          </trib>
        </valores>
      </infDPS>
    </DPS>
  </infNFSe>
</NFSe>
XML;

        $out = DanfseXmlNormalizer::prepareForDanfseMapper($xml);

        self::assertMatchesRegularExpression(
            '#<tribMun>.*<vBC>50\.00</vBC>.*</tribMun>#s',
            $out
        );
        self::assertMatchesRegularExpression(
            '#<tribMun>.*<pAliq>5\.00</pAliq>.*</tribMun>#s',
            $out
        );
        self::assertMatchesRegularExpression(
            '#<tribMun>.*<vISSQN>2\.50</vISSQN>.*</tribMun>#s',
            $out
        );
        // Bloco da NFSe continua com os valores originais da SEFIN.
        self::assertStringContainsString('<pAliqAplic>2.00</pAliqAplic>', $out);
    }
}

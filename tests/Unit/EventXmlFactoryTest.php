<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Exception\ValidationException;
use SefinSdk\Support\EventXmlFactory;

final class EventXmlFactoryTest extends TestCase
{
    private const CHAVE = '33045572234265017000192000000000004626070208392208';

    /**
     * @return array<string, mixed>
     */
    private static function baseParams(): array
    {
        return [
            'tpAmb' => '2',
            'verAplic' => 'sefin-sdk',
            'dhEvento' => '2026-07-07T09:30:00-03:00',
            'CNPJAutor' => '34265017000192',
            'chNFSe' => self::CHAVE,
            'cMotivo' => '1',
        ];
    }

    public function testForCancellationBuildsLayoutV101WithValidId(): void
    {
        $xml = EventXmlFactory::forCancellation(self::baseParams());

        $expectedId = 'PRE' . self::CHAVE . EventXmlFactory::TIPO_EVENTO_CANCELAMENTO;

        self::assertStringContainsString('<infPedReg Id="' . $expectedId . '"', $xml);
        self::assertStringContainsString('<tpAmb>2</tpAmb>', $xml);
        self::assertStringContainsString('<verAplic>sefin-sdk</verAplic>', $xml);
        self::assertStringContainsString('<dhEvento>2026-07-07T09:30:00-03:00</dhEvento>', $xml);
        self::assertStringContainsString('<CNPJAutor>34265017000192</CNPJAutor>', $xml);
        self::assertStringContainsString('<chNFSe>' . self::CHAVE . '</chNFSe>', $xml);
        self::assertStringContainsString('<e101101>', $xml);
        self::assertStringContainsString('<xDesc>Cancelamento de NFS-e</xDesc>', $xml);
        self::assertStringContainsString('<cMotivo>1</cMotivo>', $xml);
        self::assertStringContainsString('<xMotivo>Erro na emissão da NFS-e.</xMotivo>', $xml);
        self::assertStringNotContainsString('<tpEvento>', $xml);
        self::assertStringNotContainsString('<detEvento', $xml);
        self::assertStringNotContainsString('<cMotCancNFSe>', $xml);
    }

    public function testForCancellationAcceptsLegacyMotivoAliases(): void
    {
        $params = self::baseParams();
        unset($params['cMotivo']);
        $params['cMotCancNFSe'] = '4';
        $params['xMotCancNFSe'] = 'Motivo detalhado informado pelo emitente.';

        $xml = EventXmlFactory::forCancellation($params);

        self::assertStringContainsString('<cMotivo>9</cMotivo>', $xml);
        self::assertStringContainsString('<xMotivo>Motivo detalhado informado pelo emitente.</xMotivo>', $xml);
    }

    public function testForCancellationRequiresExplicitMotivoForOutros(): void
    {
        $params = self::baseParams();
        $params['cMotivo'] = '9';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('xMotivo is required when cMotivo is 9');

        EventXmlFactory::forCancellation($params);
    }

    public function testForCancellationRejectsLegacyDuplicidadeMotivo(): void
    {
        $params = self::baseParams();
        $params['cMotivo'] = '3';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('no longer supported');

        EventXmlFactory::forCancellation($params);
    }
}

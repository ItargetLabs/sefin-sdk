<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SefinSdk\Support\DpsXmlFactory;

final class DpsXmlFactoryTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function basePayload(): array
    {
        return [
            'infDPS' => [
                'tpAmb' => 2,
                'dhEmi' => '2026-06-30T08:54:02+00:00',
                'verAplic' => 'TSPD_1.0.1',
                'serie' => '1',
                'nDPS' => '1',
                'dCompet' => '2026-06-30',
                'tpEmit' => 1,
                'cLocEmi' => '3304557',
                'prest' => [
                    'CNPJ' => '34265017000192',
                    'regTrib' => ['opSimpNac' => 1, 'regEspTrib' => 0],
                ],
                'serv' => [
                    'locPrest' => ['cLocPrestacao' => '3304557'],
                    'cServ' => [
                        'cTribNac' => '120801',
                        'cTribMun' => '001',
                        'xDescServ' => 'INSCRIÇÃO PARA CONGRESSO',
                    ],
                    'atvEvento' => [
                        'xNome' => '58° CONGRESSO BRASILEIRO DE PATOLOGIA CLÍNICA',
                        'dtIni' => '2026-09-01',
                        'dtFim' => '2026-09-05',
                        'end' => [
                            'CEP' => '20040020',
                            'xLgr' => 'Av. Churchill',
                            'nro' => '94',
                            'xBairro' => 'Centro',
                        ],
                    ],
                ],
                'valores' => [
                    'vServPrest' => ['vServ' => '8560.00'],
                    'trib' => [
                        'tribMun' => ['tribISSQN' => 1, 'tpRetISSQN' => 1],
                    ],
                ],
            ],
        ];
    }

    public function testFromArrayIncludesAtvEventoForItem12Service(): void
    {
        $xml = DpsXmlFactory::fromArray(self::basePayload());

        self::assertStringContainsString('<atvEvento>', $xml);
        self::assertStringContainsString('<xNome>58° CONGRESSO BRASILEIRO DE PATOLOGIA CLÍNICA</xNome>', $xml);
        self::assertStringContainsString('<dtIni>2026-09-01</dtIni>', $xml);
        self::assertStringContainsString('<dtFim>2026-09-05</dtFim>', $xml);
        self::assertStringContainsString('<CEP>20040020</CEP>', $xml);
        self::assertStringContainsString('<xLgr>Av. Churchill</xLgr>', $xml);
    }

    public function testFromArraySupportsIdAtvEvtInsteadOfEnd(): void
    {
        $payload = self::basePayload();
        unset($payload['infDPS']['serv']['atvEvento']['end']);
        $payload['infDPS']['serv']['atvEvento']['idAtvEvt'] = 'EVT-2026-001';

        $xml = DpsXmlFactory::fromArray($payload);

        self::assertStringContainsString('<idAtvEvt>EVT-2026-001</idAtvEvt>', $xml);
        self::assertStringNotContainsString('<CEP>', $xml);
    }
}

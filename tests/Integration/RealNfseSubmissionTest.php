<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Exception\ApiException;
use SefinSdk\Exception\TransportException;
use SefinSdk\Sefin;
use SefinSdk\Support\DpsXmlFactory;
use SefinSdk\Support\DpsXmlSigner;

final class RealNfseSubmissionTest extends TestCase
{
    private static function env(string $key): string
    {
        $value = (string) (getenv($key) ?: '');

        // Comum em arquivos .env: valores entre aspas para suportar espaços.
        return trim($value, " \t\n\r\0\x0B\"'");
    }

    private static function extractDpsFromNfseXml(string $nfseXml): string
    {
        $nfseXml = trim($nfseXml);
        if ($nfseXml === '') {
            return '';
        }

        // O arquivo anexo é um XML de NFSe que contém um bloco <DPS> completo (incluindo Signature).
        // Aqui extraímos o primeiro <DPS ...>...</DPS> para enviar na rota de submissão.
        if (preg_match('~(<DPS\\b[\\s\\S]*?</DPS>)~i', $nfseXml, $m) !== 1) {
            return '';
        }

        return (string) $m[1];
    }

    private static function forceTpAmb(string $dpsXml, int $tpAmb): string
    {
        if ($tpAmb <= 0) {
            return $dpsXml;
        }

        // Mantém o XML o mais fiel possível ao original, trocando apenas o valor do <tpAmb>.
        $updated = preg_replace('~<tpAmb>\\s*\\d+\\s*</tpAmb>~i', '<tpAmb>' . $tpAmb . '</tpAmb>', $dpsXml, 1);

        return is_string($updated) ? $updated : $dpsXml;
    }

    /**
     * Converte o XML de referência (NFSe contendo DPS) em payload array para submitNfseFromArray().
     *
     * @return array<string, mixed>
     */
    private static function nfseXmlToDpsPayload(string $nfseXml, int $tpAmb): array
    {
        $dpsXml = self::extractDpsFromNfseXml($nfseXml);
        if ($dpsXml === '') {
            throw new \RuntimeException('Unable to extract DPS XML from NFSe reference.');
        }

        $dpsXml = self::forceTpAmb($dpsXml, $tpAmb);

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if ($doc->loadXML($dpsXml) !== true) {
            throw new \RuntimeException('Unable to parse extracted DPS XML.');
        }

        $xp = new \DOMXPath($doc);
        $xp->registerNamespace('nfse', 'http://www.sped.fazenda.gov.br/nfse');

        $q = static function (string $xpath) use ($xp): string {
            $n = $xp->query($xpath)->item(0);
            return $n instanceof \DOMNode ? trim($n->textContent) : '';
        };

        $qa = static function (string $xpath, string $attr) use ($xp): string {
            $n = $xp->query($xpath)->item(0);
            return $n instanceof \DOMElement ? (string) $n->getAttribute($attr) : '';
        };

        $xDescServ = $q('//nfse:DPS/nfse:infDPS/nfse:serv/nfse:cServ/nfse:xDescServ');
        // Mantém quebras de linha como no XML de referência.
        $xDescServ = str_replace(["\r\n", "\r"], "\n", $xDescServ);

        $payload = [
            'infDPS' => [
                'Id' => $qa('//nfse:DPS/nfse:infDPS', 'Id'),
                'tpAmb' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:tpAmb'),
                'dhEmi' => $q('//nfse:DPS/nfse:infDPS/nfse:dhEmi'),
                'verAplic' => $q('//nfse:DPS/nfse:infDPS/nfse:verAplic'),
                'serie' => $q('//nfse:DPS/nfse:infDPS/nfse:serie'),
                'nDPS' => $q('//nfse:DPS/nfse:infDPS/nfse:nDPS'),
                'dCompet' => $q('//nfse:DPS/nfse:infDPS/nfse:dCompet'),
                'tpEmit' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:tpEmit'),
                'cLocEmi' => $q('//nfse:DPS/nfse:infDPS/nfse:cLocEmi'),
                'prest' => [
                    'CNPJ' => $q('//nfse:DPS/nfse:infDPS/nfse:prest/nfse:CNPJ'),
                    'CPF' => $q('//nfse:DPS/nfse:infDPS/nfse:prest/nfse:CPF'),
                    'email' => $q('//nfse:DPS/nfse:infDPS/nfse:prest/nfse:email'),
                    'regTrib' => [
                        'opSimpNac' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:prest/nfse:regTrib/nfse:opSimpNac'),
                        'regEspTrib' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:prest/nfse:regTrib/nfse:regEspTrib'),
                    ],
                ],
                'toma' => [
                    'CNPJ' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:CNPJ'),
                    'CPF' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:CPF'),
                    'xNome' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:xNome'),
                    'end' => [
                        'endNac' => [
                            'cMun' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:endNac/nfse:cMun'),
                            'CEP' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:endNac/nfse:CEP'),
                        ],
                        'xLgr' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:xLgr'),
                        'nro' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:nro'),
                        'xCpl' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:xCpl'),
                        'xBairro' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:end/nfse:xBairro'),
                    ],
                    'fone' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:fone'),
                    'email' => $q('//nfse:DPS/nfse:infDPS/nfse:toma/nfse:email'),
                ],
                'serv' => [
                    'locPrest' => [
                        'cLocPrestacao' => $q('//nfse:DPS/nfse:infDPS/nfse:serv/nfse:locPrest/nfse:cLocPrestacao'),
                    ],
                    'cServ' => [
                        'cTribNac' => $q('//nfse:DPS/nfse:infDPS/nfse:serv/nfse:cServ/nfse:cTribNac'),
                        'cTribMun' => $q('//nfse:DPS/nfse:infDPS/nfse:serv/nfse:cServ/nfse:cTribMun'),
                        'xDescServ' => $xDescServ,
                    ],
                ],
                'valores' => [
                    'vServPrest' => [
                        'vServ' => $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:vServPrest/nfse:vServ'),
                    ],
                    'trib' => [
                        'tribMun' => [
                            'tribISSQN' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:trib/nfse:tribMun/nfse:tribISSQN'),
                            'tpRetISSQN' => (int) $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:trib/nfse:tribMun/nfse:tpRetISSQN'),
                        ],
                        'totTrib' => [
                            'vTotTrib' => [
                                'vTotTribFed' => $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:trib/nfse:totTrib/nfse:vTotTrib/nfse:vTotTribFed'),
                                'vTotTribEst' => $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:trib/nfse:totTrib/nfse:vTotTrib/nfse:vTotTribEst'),
                                'vTotTribMun' => $q('//nfse:DPS/nfse:infDPS/nfse:valores/nfse:trib/nfse:totTrib/nfse:vTotTrib/nfse:vTotTribMun'),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Remove chaves vazias de documento (CPF/CNPJ) para não poluir o XML final.
        if (trim((string) $payload['infDPS']['prest']['CPF']) === '') {
            unset($payload['infDPS']['prest']['CPF']);
        }
        if (trim((string) $payload['infDPS']['prest']['CNPJ']) === '') {
            unset($payload['infDPS']['prest']['CNPJ']);
        }
        if (trim((string) $payload['infDPS']['toma']['CPF']) === '') {
            unset($payload['infDPS']['toma']['CPF']);
        }
        if (trim((string) $payload['infDPS']['toma']['CNPJ']) === '') {
            unset($payload['infDPS']['toma']['CNPJ']);
        }

        return $payload;
    }

    /**
     * Gera uma nova combinação serie/nDPS (e, consequentemente, novo Id) para evitar duplicidade (E0014).
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private static function bumpDpsIdentity(array $payload): array
    {
        if (!isset($payload['infDPS']) || !is_array($payload['infDPS'])) {
            return $payload;
        }

        $inf = $payload['infDPS'];

        $now = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));

        // nDPS precisa ser numérico e único; usamos epoch em ms (13 dígitos) para evitar colisão.
        $newNDps = (string) ((int) floor(microtime(true) * 1000));

        $inf['nDPS'] = $newNDps;
        $inf['dhEmi'] = $now->format('c');
        $inf['dCompet'] = $now->format('Y-m-d');

        // Limpa Id para a fábrica derivar um Id válido (TSIdDPS) com base nos campos atualizados.
        unset($inf['Id'], $inf['id']);

        $payload['infDPS'] = $inf;

        return $payload;
    }

    /**
     * @group integration
     */
    public function testRealRequestToSefinRestrictedProductionEndpoint(): void
    {
        if (self::env('SEFIN_REAL_INTEGRATION') !== '1') {
            self::markTestSkipped('Defina SEFIN_REAL_INTEGRATION=1 para rodar este teste de integração real.');
        }

        $certPath = self::env('SEFIN_CERT_PATH');
        $privateKeyPath = self::env('SEFIN_PRIVATE_KEY_PATH');
        $privateKeyPassword = self::env('SEFIN_PRIVATE_KEY_PASSWORD');
        if ($certPath === '') {
            self::markTestSkipped('Defina SEFIN_CERT_PATH apontando para o .pem do cliente.');
        }

        if (!is_file($certPath)) {
            self::markTestSkipped(sprintf('Certificado não encontrado em: %s', $certPath));
        }

        if (!is_file($privateKeyPath)) {
            self::markTestSkipped(sprintf('Chave privada não encontrada em: %s', $privateKeyPath));
        }


        $nfseXmlPath = 'file.xml';

        if ($nfseXmlPath === '') {
            self::markTestSkipped('Defina SEFIN_NFSE_XML_PATH apontando para o XML de NFSe (anexo) para extrair a DPS e enviar.');
        }

        if (!is_file($nfseXmlPath)) {
            self::markTestSkipped(sprintf('Arquivo XML de NFSe não encontrado em: %s', $nfseXmlPath));
        }

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig($certPath, $privateKeyPath, $privateKeyPassword !== '' ? $privateKeyPassword : null)
        );

        $nfseXml = (string) file_get_contents($nfseXmlPath);
        $payload = self::nfseXmlToDpsPayload($nfseXml, Environment::restrictedProduction()->getType()->value);
        $payload = self::bumpDpsIdentity($payload);

        try {
            $response = $sdk->submitNfseFromArray($payload);
            self::assertSame(Environment::restrictedProduction()->getType()->value, $response->tipoAmbiente);
            self::assertNotSame('', $response->chaveAcesso);
            self::assertNotSame('', $response->nfseXmlGZipB64);
            self::assertNotSame('', $response->decodedXml());
        } catch (ApiException $e) {
            // Sempre gera artefatos de debug para facilitar validar contra o XML de referência.
            @mkdir(__DIR__ . '/../../build', 0777, true);
            file_put_contents(__DIR__ . '/../../build/last-payload.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Gera o XML assinado exatamente como a SDK enviaria, para inspeção local.
            $certPemRaw = (string) file_get_contents($certPath);
            $keyPemRaw = (string) file_get_contents($privateKeyPath);
            $certPem = self::extractPemBlock($certPemRaw, 'CERTIFICATE');
            $keyPem = self::extractAnyPrivateKeyPem($keyPemRaw);

            $dpsXml = DpsXmlFactory::fromArray($payload);
            $signed = DpsXmlSigner::signInfDps($dpsXml, $keyPem, $certPem, $privateKeyPassword !== '' ? $privateKeyPassword : null);
            file_put_contents(__DIR__ . '/../../build/last-dps.xml', $signed);

            file_put_contents(__DIR__ . '/../../build/last-api-error.json', json_encode($e->getPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            throw $e;
        } catch (TransportException $e) {
            throw $e;
        }
    }

    private static function extractPemBlock(string $input, string $label): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        $pattern = sprintf('~-----BEGIN\\s+%s-----[\\s\\S]*?-----END\\s+%s-----~', preg_quote($label, '~'), preg_quote($label, '~'));
        if (preg_match($pattern, $input, $m) === 1) {
            return (string) $m[0];
        }

        return $input;
    }

    private static function extractAnyPrivateKeyPem(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        foreach (['PRIVATE KEY', 'ENCRYPTED PRIVATE KEY', 'RSA PRIVATE KEY', 'EC PRIVATE KEY'] as $label) {
            $pem = self::extractPemBlock($input, $label);
            if (str_contains($pem, '-----BEGIN ' . $label . '-----')) {
                return $pem;
            }
        }

        return $input;
    }
}

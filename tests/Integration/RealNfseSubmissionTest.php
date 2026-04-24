<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Dto\NfseSubmissionRequest;
use SefinSdk\Exception\ApiException;
use SefinSdk\Exception\TransportException;
use SefinSdk\Sefin;

final class RealNfseSubmissionTest extends TestCase
{
    private static function env(string $key): string
    {
        $value = (string) (getenv($key) ?: '');

        // Comum em arquivos .env: valores entre aspas para suportar espaços.
        return trim($value, " \t\n\r\0\x0B\"'");
    }

    /**
     * @group integration
     */
    public function testRealRequestToSefinRestrictedProductionEndpoint(): void
    {
        if (self::env('SEFIN_REAL_INTEGRATION') !== '1') {
            self::markTestSkipped('Defina SEFIN_REAL_INTEGRATION=1 para rodar este teste de integração real.');
        }

        $prestCnpj = self::env('SEFIN_PREST_CNPJ');
        $municipioIbge = self::env('SEFIN_MUNICIPIO_IBGE');

        if ($prestCnpj === '' || $municipioIbge === '') {
            self::markTestSkipped('Defina SEFIN_PREST_CNPJ e SEFIN_MUNICIPIO_IBGE para montar uma DPS mínima válida.');
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

        if ($privateKeyPath === '') {
            self::markTestSkipped('Defina SEFIN_PRIVATE_KEY_PATH apontando para o .key/.pem da chave privada.');
        }

        if (!is_file($privateKeyPath)) {
            self::markTestSkipped(sprintf('Chave privada não encontrada em: %s', $privateKeyPath));
        }

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig($certPath, $privateKeyPath, $privateKeyPassword !== '' ? $privateKeyPassword : null)
        );
        $serieDps = "";
        $numeroDps = "";

        $now = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
        $cMun  = str_pad($municipioIbge, 7, '0', STR_PAD_LEFT);
        $tpInsc = '2'; // 1=CPF, 2=CNPJ
        $insc  = str_pad(preg_replace('/\D+/', '', $prestCnpj), 14, '0', STR_PAD_LEFT);
        $serie = str_pad($serieDps, 5, '0', STR_PAD_LEFT);
        $nDps  = str_pad($numeroDps, 15, '0', STR_PAD_LEFT);
        $id = 'DPS' . $cMun . $tpInsc . $insc . $serie . $nDps;

        $request = NfseSubmissionRequest::fromXml(
            '<DPS xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01">'
                . '<infDPS id="' . $id . '">'
                . '<tpAmb>2</tpAmb>'
                . '<dhEmi>' . $now->format('c') . '</dhEmi>'
                . '<serie>' . $serieDps . '</serie>'
                . '<nDPS>' . $nDps . '</nDPS>'
                . '<dCompet>' . $now->format('Y-m-d') . '</dCompet>'
                . '<tpEmit>1</tpEmit>'
                . '<cLocEmi>' . $municipioIbge . '</cLocEmi>'
                . '<prest>'
                . '<CNPJ>' . $prestCnpj . '</CNPJ>'
                . '<regTrib>'
                . '<opSimpNac>2</opSimpNac>'
                . '<regEspTrib>0</regEspTrib>'
                . '</regTrib>'
                . '</prest>'
                . '<serv>'
                . '<locPrest>'
                . '<cLocPrestacao>' . $municipioIbge . '</cLocPrestacao>'
                . '<cPaisPrestacao>BR</cPaisPrestacao>'
                . '</locPrest>'
                . '<cServ>'
                . '<cTribNac>010101</cTribNac>'
                . '<xDescServ>Servico de teste</xDescServ>'
                . '</cServ>'
                . '</serv>'
                . '<valores>'
                . '<vServPrest>'
                . '<vServ>1.00</vServ>'
                . '</vServPrest>'
                . '<trib>'
                . '<tribMun>'
                . '<tribISSQN>1</tribISSQN>'
                . '<tpRetISSQN>1</tpRetISSQN>'
                . '</tribMun>'
                . '<totTrib>'
                . '<vTotTrib>'
                . '<vTotTribFed>0.00</vTotTribFed>'
                . '<vTotTribEst>0.00</vTotTribEst>'
                . '<vTotTribMun>0.00</vTotTribMun>'
                . '</vTotTrib>'
                . '<pTotTrib>'
                . '<pTotTribFed>0.00</pTotTribFed>'
                . '<pTotTribEst>0.00</pTotTribEst>'
                . '<pTotTribMun>0.00</pTotTribMun>'
                . '</pTotTrib>'
                . '<indTotTrib>0</indTotTrib>'
                . '<pTotTribSN>0.00</pTotTribSN>'
                . '</totTrib>'
                . '</trib>'
                . '</valores>'
                . '</infDPS>'
                . '</DPS>'
        );

        try {
            $response = $sdk->submitNfse($request);
            self::assertSame(Environment::restrictedProduction()->getType()->value, $response->tipoAmbiente);
            self::assertNotSame('', $response->chaveAcesso);
            self::assertNotSame('', $response->nfseXmlGZipB64);
            self::assertNotSame('', $response->decodedXml());
        } catch (ApiException $e) {
            self::assertContains($e->getCode(), [400, 401, 403, 422, 500]);
        } catch (TransportException $e) {
            self::fail('Falha de transporte (TLS/HTTP) ao chamar SEFIN: ' . $e->getMessage());
        }
    }
}

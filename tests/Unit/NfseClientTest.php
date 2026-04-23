<?php

declare(strict_types=1);

namespace SefinSdk\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Dto\NfseSubmissionRequest;
use SefinSdk\Dto\RegisterEventRequest;
use SefinSdk\Exception\ApiException;
use SefinSdk\Sefin;
use SefinSdk\Support\XmlCompressor;

final class NfseClientTest extends TestCase
{
    public function testSubmitNfseSerializesXmlAndParsesTypedResponse(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'tipoAmbiente' => 2,
                'versaoAplicativo' => '1.0.0',
                'dataHoraProcessamento' => '2026-04-23T15:00:00Z',
                'idDps' => 'DPS123',
                'chaveAcesso' => '12345678901234567890123456789012345678901234567890',
                'nfseXmlGZipB64' => XmlCompressor::encode('<NFSe>ok</NFSe>'),
                'alertas' => [
                    ['codigo' => 'W01', 'descricao' => 'Alerta de teste'],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig('/certs/client.pem'),
            new Client(['handler' => $handler, 'base_uri' => 'https://example.test/'])
        );

        $response = $sdk->submitNfse(NfseSubmissionRequest::fromXml('<DPS>abc</DPS>'));

        self::assertSame('DPS123', $response->idDps);
        self::assertSame('<NFSe>ok</NFSe>', $response->decodedXml());
        self::assertCount(1, $response->alertas);

        $requestBody = (string) $history[0]['request']->getBody();
        $decoded = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('<DPS>abc</DPS>', XmlCompressor::decode($decoded['dpsXmlGZipB64']));
    }

    public function testCanCheckIfDpsAlreadyGeneratedNfse(): void
    {
        $mock = new MockHandler([
            new Response(200),
        ]);
        $handler = HandlerStack::create($mock);

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig('/certs/client.pem'),
            new Client(['handler' => $handler, 'base_uri' => 'https://example.test/'])
        );

        self::assertTrue($sdk->hasNfseForDpsId('DPS123'));
    }

    public function testReturnsFalseWhenDpsHasNoGeneratedNfse(): void
    {
        $mock = new MockHandler([
            new Response(404),
        ]);
        $handler = HandlerStack::create($mock);

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig('/certs/client.pem'),
            new Client(['handler' => $handler, 'base_uri' => 'https://example.test/'])
        );

        self::assertFalse($sdk->hasNfseForDpsId('DPS123'));
    }

    public function testRegistersEventUsingCompressedXmlPayload(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'tipoAmbiente' => 2,
                'versaoAplicativo' => '1.0.0',
                'dataHoraProcessamento' => '2026-04-23T15:00:00Z',
                'eventoXmlGZipB64' => XmlCompressor::encode('<Evento>ok</Evento>'),
            ], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig('/certs/client.pem'),
            new Client(['handler' => $handler, 'base_uri' => 'https://example.test/'])
        );

        $response = $sdk->registerEvent(
            '12345678901234567890123456789012345678901234567890',
            RegisterEventRequest::fromXml('<Evento>123</Evento>')
        );

        self::assertSame('<Evento>ok</Evento>', $response->decodedXml());

        $requestBody = (string) $history[0]['request']->getBody();
        $decoded = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(
            '<Evento>123</Evento>',
            XmlCompressor::decode($decoded['pedidoRegistroEventoXmlGZipB64'])
        );
    }

    public function testThrowsTypedExceptionWhenApiReturnsValidationError(): void
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'tipoAmbiente' => 2,
                'versaoAplicativo' => '1.0.0',
                'dataHoraProcessamento' => '2026-04-23T15:00:00Z',
                'idDPS' => 'DPS123',
                'erros' => [
                    ['codigo' => 'E01', 'descricao' => 'XML invalido'],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);

        $sdk = new Sefin(
            Environment::restrictedProduction(),
            new CertificateConfig('/certs/client.pem'),
            new Client(['handler' => $handler, 'base_uri' => 'https://example.test/'])
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('XML invalido');

        $sdk->submitNfse(NfseSubmissionRequest::fromXml('<DPS>abc</DPS>'));
    }
}

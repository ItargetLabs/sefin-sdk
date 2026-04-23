<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;
use SefinSdk\Support\XmlCompressor;

final class NfseLookupResponse
{
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly string $chaveAcesso,
        public readonly string $nfseXmlGZipB64
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tipoAmbiente: (int) ($payload['tipoAmbiente'] ?? 0),
            versaoAplicativo: (string) ($payload['versaoAplicativo'] ?? ''),
            dataHoraProcessamento: new DateTimeImmutable((string) ($payload['dataHoraProcessamento'] ?? 'now')),
            chaveAcesso: (string) ($payload['chaveAcesso'] ?? ''),
            nfseXmlGZipB64: (string) ($payload['nfseXmlGZipB64'] ?? '')
        );
    }

    public function decodedXml(): string
    {
        return XmlCompressor::decode($this->nfseXmlGZipB64);
    }
}

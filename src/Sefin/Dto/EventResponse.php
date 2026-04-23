<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;
use SefinSdk\Support\XmlCompressor;

final class EventResponse
{
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly string $eventoXmlGZipB64
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
            eventoXmlGZipB64: (string) ($payload['eventoXmlGZipB64'] ?? '')
        );
    }

    public function decodedXml(): string
    {
        return XmlCompressor::decode($this->eventoXmlGZipB64);
    }
}

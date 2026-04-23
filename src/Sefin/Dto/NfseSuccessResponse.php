<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;
use SefinSdk\Support\XmlCompressor;

final class NfseSuccessResponse
{
    /**
     * @param list<ProcessingMessage> $alertas
     */
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly ?string $idDps,
        public readonly string $chaveAcesso,
        public readonly string $nfseXmlGZipB64,
        public readonly array $alertas = []
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $alerts = [];

        foreach (($payload['alertas'] ?? []) as $alert) {
            if (is_array($alert)) {
                $alerts[] = ProcessingMessage::fromArray($alert);
            }
        }

        return new self(
            tipoAmbiente: (int) ($payload['tipoAmbiente'] ?? 0),
            versaoAplicativo: (string) ($payload['versaoAplicativo'] ?? ''),
            dataHoraProcessamento: new DateTimeImmutable((string) ($payload['dataHoraProcessamento'] ?? 'now')),
            idDps: isset($payload['idDps']) ? (string) $payload['idDps'] : null,
            chaveAcesso: (string) ($payload['chaveAcesso'] ?? ''),
            nfseXmlGZipB64: (string) ($payload['nfseXmlGZipB64'] ?? ''),
            alertas: $alerts
        );
    }

    public function decodedXml(): string
    {
        return XmlCompressor::decode($this->nfseXmlGZipB64);
    }
}

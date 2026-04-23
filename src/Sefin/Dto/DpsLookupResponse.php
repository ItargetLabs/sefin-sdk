<?php

declare(strict_types=1);

namespace SefinSdk\Dto;

use DateTimeImmutable;

final class DpsLookupResponse
{
    public function __construct(
        public readonly int $tipoAmbiente,
        public readonly string $versaoAplicativo,
        public readonly DateTimeImmutable $dataHoraProcessamento,
        public readonly string $idDps,
        public readonly string $chaveAcesso
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
            idDps: (string) ($payload['idDps'] ?? ''),
            chaveAcesso: (string) ($payload['chaveAcesso'] ?? '')
        );
    }
}
